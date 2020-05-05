<?php

namespace App\Helpers;

use App\Campaign;
use App\Field;
use App\Institution;
use App\Student;
use App\User;
use DrewM\MailChimp\MailChimp;

class MailChimpHelper
{
    public $mc;
    public $institution;
    public $campaign;
    public $folder_id;
    public $list_id;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = Campaign::find($campaign->id); //To make sure we get the most recent version of the campaign, not the cached version
        $this->institution = Institution::find($campaign->institution_id); //To make sure we get the most recent version of the campaign, not the cached version

        $this->mc = new MailChimp($this->institution->mailchimp_key);

        if (!$this->list_id = $this->campaign->mailchimp_list_id) {
            $this->list_id = $this->get_or_create_list();
        }
    }

    public function add_student_to_campaign(Student $student)
    {
        if(!$this->campaign->mailchimp_workflow_id
            || !$this->campaign->mailchimp_workflow_email_id) {
            return 'Automation campaign not found';
        }

        $data = [
            'email_address' => $student->email,
        ];
        $response = $this->mc->post('automations/'.$this->campaign->mailchimp_workflow_id.'/emails/'.$this->campaign->mailchimp_workflow_email_id.'/queue', $data);

        return $response;
    }

    private function get_merge_fields()
    {
        $response = $this->mc->get('/lists/' . $this->list_id . '/merge-fields?count=100');

        $fields = [];
        foreach ($response['merge_fields'] as $merge_fields) {
            array_push($fields, $merge_fields['tag']);
        };

        return $fields;
    }

    public function update_merge_fields()
    {

        $sh = new StudentHelper();

        $merge_fields = $this->get_merge_fields();

        //Check that PURL exists
        if (!in_array('PURL', $merge_fields)) {
            $data = [
                'tag' => 'PURL',
                'name' => 'PURL',
                'type' => 'text'
            ];
            $this->mc->post('/lists/' . $this->list_id . '/merge-fields', $data);
        }

        //Check that Recruiter exists
        if (!in_array('RECRUITER', $merge_fields)) {
            $data = [
                'tag' => 'RECRUITER',
                'name' => 'Recruiter',
                'type' => 'text'
            ];
            $this->mc->post('/lists/' . $this->list_id . '/merge-fields', $data);
        }

        //Check that Recruiter Email exists
        if (!in_array('RECR_EMAIL', $merge_fields)) {
            $data = [
                'tag' => 'RECR_EMAIL',
                'name' => 'Recruiter Email',
                'type' => 'text'
            ];
            $this->mc->post('/lists/' . $this->list_id . '/merge-fields', $data);
        }

        //Check that roles exist
//        foreach(config('app.roles') as $role) {
//            if (!in_array(strtoupper($role), $merge_fields)) {
//                if($role == 'recruiter') {
//                    $data = [
//                        'tag' => strtoupper(substr($role,0,10)),
//                        'name' => ucfirst($role),
//                        'type' => 'text'
//                    ];
//                    $this->mc->post('/lists/' . $this->list_id . '/merge-fields', $data);
//                }
//            }
//        }

        //Check that all standard field exist
        foreach ($sh->standard_field_names as $standard_field) {

            $tag = $sh->format_field_for_mailchimp_tag($standard_field);

            if (!in_array(strtoupper($tag), $merge_fields)) {
                $this->create_tag($standard_field, $tag);
            }
        }

        //Check that all custom fields exist
        foreach (Field::where('institution_id', $this->institution->id)->get() as $custom_field) {

            if (!in_array($custom_field->tag, $merge_fields)) {
                $this->create_tag($custom_field->name, $custom_field->tag);
            }
        }
    }

    public function create_tag($name, $tag) {
        $data = [
            'tag' => $tag,
            'name' => ucfirst($name),
            'type' => 'text'
        ];

        $response = $this->mc->post('/lists/' . $this->list_id . '/merge-fields', $data);

        return $response;
    }

    public function rename_merge_field($old_tag, $field)
    {
        $sh = new StudentHelper();

        //Find existing merge field id
        $response = $this->mc->get('/lists/' . $this->list_id . '/merge-fields?count=100');
        foreach ($response['merge_fields'] as $merge_fields) {
            if($merge_fields['tag'] == $field['tag']) {
                $merge_field_id = $merge_fields['merge_id'];
                break;
            }
        };

        //Update if found
        if(isset($merge_field_id)) {
            $data = [
                'tag' => $field['tag'],
                'name' => $field['name']
            ];

            $response = $this->mc->patch('/lists/' . $this->list_id . '/merge-fields/'.$merge_field_id, $data);

            return $response;
        }
        //Otherwise create a new field
        else {
            $this->create_tag($field['name'], $field['tag']);
        }

        return false;

    }

    public function delete_merge_field($tag)
    {
        //Find existing merge field id
        $merge_field_id = '';
        $response = $this->mc->get('/lists/' . $this->list_id . '/merge-fields');
        if($response['merge_fields']) {
            foreach ($response['merge_fields'] as $merge_fields) {
                if($merge_fields['tag'] == $tag) {
                    $merge_field_id = $merge_fields['merge_id'];
                    break;
                }
            };
        }


        if($merge_field_id) {

            $response = $this->mc->delete('/lists/' . $this->list_id . '/merge-fields/'.$merge_field_id);

            return $response;
        }

        return false;

    }

    public function match_student_data_to_merge_fields(Student $student)
    {

        $sh = new StudentHelper();

        $data = [];
        $data['PURL'] = $sh->get_purl_url($student);

        //Standard Fields
        foreach ($sh->standard_field_names as $standard_field) {
            if ($standard_field == 'email') continue;
            $merge_field = $sh->format_field_for_mailchimp_tag($standard_field);
            if($student->{$standard_field})
                $data[$merge_field] = $student->{$standard_field};
        }

        //Recruiter
        $recruiter = $student->staff->where('role','recruiter')->first();
        if($recruiter) {
            $user = User::find($recruiter->user_id);
            $data['RECRUITER'] = $recruiter->name;
            $data['RECR_EMAIL'] = $user->email;
        }


        //Roles
//        foreach(config('app.roles') as $role) {
//            if(isset($student->staff->where('role',$role)->first()->name)) {
//                $merge_field = $sh->format_field_for_mailchimp_tag($role);
//                $data[$merge_field] = $student->staff->where('role',$role)->first()->name;
//            }
//        }

        //Custom Fields
        $student_fields = $sh->get_student_custom_fields($student);
        foreach (Field::where('institution_id', $this->institution->id)->get() as $custom_field) {
            $merge_field = $custom_field->tag;
            if($student_fields[$custom_field->id])
                $data[$merge_field] = $student_fields[$custom_field->id];
        }

        return (object) $data;

    }


    public function add_student(Student $student)
    {
        if (!$this->list_id) {
            $this->get_or_create_list();
        }

        $data = [
            'email_address' => $student->email,
            'status' => 'subscribed',
            'merge_fields' => $this->match_student_data_to_merge_fields($student)
        ];
        $response = $this->mc->post('/lists/' . $this->list_id . '/members', $data);

        if($response['status'] == 400) {
//            if($response['title'] == 'Member Exists') {
//                $response = $this->update_student($student);
//                $student->mailchimp_member_id = $response['id'];
//                $student->save();
//            } else {
//                dd($response);
//            }
        } else {
            $student->mailchimp_member_id = $response['id'];
            $student->save();
        }

        return $response;
    }

    public function get_student(Student $student)
    {
        if (!$this->list_id) {
            $this->get_or_create_list();
        }

        if(!$student->mailchimp_member_id) return false;

        $response = $this->mc->get('/lists/' . $this->list_id . '/members/' . $student->mailchimp_member_id);

        if($response['status'] == 400) {
//            if($response['title'] == 'Member Exists') {
//                $response = $this->update_student($student);
//                $student->mailchimp_member_id = $response['id'];
//                $student->save();
//            } else {
//                dd($response);
//            }
        } else {
            $student->mailchimp_member_id = $response['id'];
            $student->save();
        }

        return $response;
    }

    public function update_student(Student $student, $original_email = null)
    {
        if($original_email) {
            $email = $original_email;
        } else {
            $email = $student->email;
        }

        $data = [
            'email_address' => $student->email,
            'status' => 'subscribed',
            'merge_fields' => $this->match_student_data_to_merge_fields($student)
        ];

        //echo '/lists/' . $this->list_id . '/members/' . md5(strtolower($email));
        //exit;

        $response = $this->mc->patch('/lists/' . $this->list_id . '/members/' . md5(strtolower($email)), $data);

        return $response;
    }

    public function destroy_member(Student $student)
    {

        $response = $this->mc->delete('/lists/' . $this->list_id . '/members/' . md5($student->email));

        return $response;
    }


    public function trigger_emails($email)
    {
        $data = ['email_address' => $email];
        $response = $this->mc->post('automations/3a43f59945/emails/127ba76743/queue', $data);
    }

    public function get_or_create_list()
    {
        $lists = $this->mc->get('lists');
        if($lists) {
            //See if list already exists for this Campaign
            foreach ($lists['lists'] as $list) {
                if ($list['name'] == $this->campaign->name) {
                    $this->list_id = $list['id'];

                    $this->campaign->mailchimp_list_id = $list['id'];
                    $this->campaign->save();

                    return $list['id'];
                }
            }
        }

        //Else, create a new list for this Institution
        $data = [
            'name' => $this->campaign->name,
            'contact' => [
                'company' => $this->campaign->institution->name,
                'address1' => $this->campaign->institution->address,
                'city' => $this->campaign->institution->city,
                'state' => $this->campaign->institution->state,
                'zip' => $this->campaign->institution->zip,
                'country' => 'US',
            ],
            'permission_reminder' => 'You signed up on our website',
            'campaign_defaults' => [
                'from_name' => $this->campaign->institution->contact,
                'from_email' => $this->campaign->institution->user->email,
                'subject' => $this->campaign->institution->name,
                'language' => 'english',
            ],
            'email_type_option' => false
        ];

        $response = $this->mc->post('lists', $data);

        $this->list_id = $response['id'];

        $this->campaign->mailchimp_list_id = $response['id'];
        $this->campaign->save();

        return $response['id'];
    }

    public function get_or_create_campaign_folder()
    {
        $folders = $this->mc->get('campaign-folders');

        //See if folder already exists for this Institution
        foreach ($folders['folders'] as $folder) {
            if ($folder['name'] == $this->institution->name) {
                $this->folder_id = $folder['id'];
                return $folder['id'];
            }
        }

        //Else, create a new folder for this Institution
        $data = ['name' => $this->institution->name];
        $response = $this->mc->post('campaign-folders', $data);

        $this->folder_id = $response['id'];
        return $response['id'];
    }

    public function check_for_campaign()
    {

        if (!$this->list_id) {
            $this->get_or_create_list();
        }

        $automations = $this->mc->get('automations');

        if($automations) {
            foreach ($automations['automations'] as $automation) {

                if ($automation['recipients']['list_id'] == $this->list_id
                ) {
                    $this->campaign->mailchimp_workflow_id = $automation['id'];
                    $this->campaign->save();

                    $emails = $this->mc->get('automations/'.$automation['id'].'/emails');
                    foreach($emails['emails'] as $email) {
                        if($email['position'] == 1) {
                            $this->campaign->mailchimp_workflow_email_id = $email['id'];
                            $this->campaign->save();
                            return true;
                        }
                    }
                }

            }
        }


        return false;

    }
}