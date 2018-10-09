<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{

    protected $toTruncate = ['users','institutions','staff',
                            'campaigns','students','fields','field_student',
                            'rules'];


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->truncate();

        //Insert Admin
        DB::table('users')->insert([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'type' => 'admin',
        ]);

        //Insert Institution
        DB::table('users')->insert([
            'email' => 'manager@test.com',
            'password' => bcrypt('password'),
            'type' => 'institution',
        ]);
        DB::table('institutions')->insert([
            'user_id' => 2,
            'name' => 'Bradley University',
            'contact' => 'Bob Manager',
            'address' => '123 Main St',
            'city' => 'Chicago',
            'state' => 'IL',
            'zip' => '60451',
            'mailchimp_key' => '853bc0f022ed3913942a3f406fb93f75-us2'
        ]);

        //Insert Recruiter 1
        DB::table('users')->insert([
            'email' => 'recruiter@test.com',
            'password' => bcrypt('password'),
            'type' => 'staff',
        ]);
        $recruiter1 = DB::table('staff')->insert([
            'user_id' => 3,
            'institution_id' => 1,
            'name' => 'Sally Recruiter',
            'role' => 'recruiter',
        ]);

        //Insert Recruiter 2
        $recruiter2 = DB::table('users')->insert([
            'email' => 'jimstaff@test.com',
            'password' => bcrypt('password'),
            'type' => 'staff',
        ]);
        DB::table('staff')->insert([
            'user_id' => 4,
            'institution_id' => 1,
            'name' => 'Jim Recruiter',
            'role' => 'recruiter',
        ]);

        //Insert Coaches
        $coach1 = DB::table('staff')->insert([
            'user_id' => 1,
            'institution_id' => 1,
            'name' => 'Coach Tom',
            'role' => 'coach',
        ]);
        $coach2 = DB::table('staff')->insert([
            'user_id' => 1,
            'institution_id' => 1,
            'name' => 'Coach Bill',
            'role' => 'coach',
        ]);
        $coach3 = DB::table('staff')->insert([
            'user_id' => 1,
            'institution_id' => 1,
            'name' => 'Coach Ann',
            'role' => 'coach',
        ]);

        //Insert Professors
        DB::table('staff')->insert([
            'user_id' => 1,
            'institution_id' => 1,
            'name' => 'Dr. Susan',
            'role' => 'professor',
        ]);

        DB::table('staff')->insert([
            'user_id' => 1,
            'institution_id' => 1,
            'name' => 'Dr. Mike',
            'role' => 'professor',
        ]);

        DB::table('staff')->insert([
            'user_id' => 1,
            'institution_id' => 1,
            'name' => 'Dr. Dale',
            'role' => 'professor',
        ]);

        $ch = new \App\Helpers\CampaignHelper();

        //Insert Campaign 1
        $directory = 'Campaign2017';
        $ch->install_lp_files($directory);
        DB::table('campaigns')->insert([
            'institution_id' => 1,
            'name' => 'Campaign 2017',
            'directory' => 'campaigns/'.$directory,
            'domain' => 'recruitu:8888',
            'ftp_username' => 'test',
            'ftp_path' => '/',
        ]);

        //Insert Campaign 2
        $directory = 'Campaign2018';
        $ch->install_lp_files($directory);
        DB::table('campaigns')->insert([
            'institution_id' => 1,
            'name' => $directory,
            'directory' => 'campaigns/'.$directory,
            'domain' => 'recruitu:8888',
            'ftp_username' => 'test',
            'ftp_path' => '/',
        ]);

        //Insert Fields
        DB::table('fields')->insert([
            'institution_id' => 1,
            'name' => 'Location',
            'tag' => 'LOCATION'
        ]);

        DB::table('fields')->insert([
            'institution_id' => 1,
            'name' => 'Major',
            'tag' => 'MAJOR'
        ]);

        DB::table('fields')->insert([
            'institution_id' => 1,
            'name' => 'Extracurricular',
            'tag' => 'EXTRACURRI'
        ]);

        //Recruiter 1 Rules
        DB::table('rules')->insert([
            'institution_id' => 1,
            'staff_id' => 1,
            'field_id' => 1,
            'operator' => 'equals',
            'value' => 'Chicago',
        ]);

        //Recruiter 2 Rules
        DB::table('rules')->insert([
            'institution_id' => 1,
            'staff_id' => 2,
            'field_id' => 1,
            'operator' => 'equals',
            'value' => 'New York',
        ]);

        //Professor Rules
        DB::table('rules')->insert([
            'institution_id' => 1,
            'staff_id' => 6,
            'field_id' => 2,
            'operator' => 'equals',
            'value' => 'Biology',
        ]);
        DB::table('rules')->insert([
            'institution_id' => 1,
            'staff_id' => 7,
            'field_id' => 2,
            'operator' => 'equals',
            'value' => 'Nursing',
        ]);
        DB::table('rules')->insert([
            'institution_id' => 1,
            'staff_id' => 8,
            'field_id' => 2,
            'operator' => 'equals',
            'value' => 'Business',
        ]);

        //Coach Rules
        DB::table('rules')->insert([
            'institution_id' => 1,
            'staff_id' => 3,
            'field_id' => 3,
            'operator' => 'equals',
            'value' => 'Hockey',
        ]);
        DB::table('rules')->insert([
            'institution_id' => 1,
            'staff_id' => 4,
            'field_id' => 3,
            'operator' => 'equals',
            'value' => 'Soccer',
        ]);
        DB::table('rules')->insert([
            'institution_id' => 1,
            'staff_id' => 5,
            'field_id' => 3,
            'operator' => 'equals',
            'value' => 'Football',
        ]);


        factory('App\Student',50)->create();

        //Give students custom field values
        $locations = ['Chicago','New York'];
        $majors = ['Nursing','Biology','Business',''];
        $sports = ['Hockey','Soccer','Football',''];
        $links = ['http://google.com','http://yahoo.com','http://zillow.com'];

        foreach(\App\Student::all() as $i => $student) {

            if($i < 15) {
                $date = \Carbon\Carbon::now()->subDays(1);
            }
            elseif($i < 25) {
                $date = \Carbon\Carbon::now()->subDays(2);
            }
            elseif($i < 30) {
                $date = \Carbon\Carbon::now()->subDays(3);
            }
            elseif($i < 35) {
                $date = \Carbon\Carbon::now()->subDays(7);
            }
            elseif($i < 40) {
                $date = \Carbon\Carbon::now()->subDays(8);
            }
            elseif($i < 50) {
                $date = \Carbon\Carbon::now()->subDays(9);
            }

            //Location
            $fs = new \App\FieldStudent();
            $fs->student_id = $student->id;
            $fs->field_id = 1;
            $fs->value = $locations[rand(0,1)];
            $fs->save();

            //Major
            $fs = new \App\FieldStudent();
            $fs->student_id = $student->id;
            $fs->field_id = 2;
            $fs->value = $majors[rand(0,3)];
            $fs->save();

            //Sport
            $fs = new \App\FieldStudent();
            $fs->student_id = $student->id;
            $fs->field_id = 3;
            $fs->value = $sports[rand(0,3)];
            $fs->save();

            //Match to Staff
            \App\Helpers\StudentHelper::match_to_staff($student);

            //Add Results
            if(rand(0,1) == rand(0,1)) continue;

            $result = new \App\Result();
            $result->campaign_id = $student->campaign_id;
            $result->student_id = $student->id;
            $result->ip = '24.14.206.100';
            $result->url = 'http://domain.com/index.html';
            $result->page = 'home';
            $result->created_at = $date;
            $result->save();

            if(rand(0,1) == rand(0,1)) continue;

            $result = new \App\Result();
            $result->campaign_id = $student->campaign_id;
            $result->student_id = $student->id;
            $result->ip = '24.14.206.100';
            $result->url = 'http://domain.com/page2.html';;
            $result->page = 'page2';
            $result->created_at = $date;
            $result->save();

            $result = new \App\Result();
            $result->campaign_id = $student->campaign_id;
            $result->student_id = $student->id;
            $result->ip = '24.14.206.100';
            $result->url = $links[rand(0,2)];
            $result->page = 'link';
            $result->created_at = $date;
            $result->save();

            if(rand(0,1) == rand(0,1)) continue;

            $result = new \App\Result();
            $result->campaign_id = $student->campaign_id;
            $result->student_id = $student->id;
            $result->ip = '24.14.206.100';
            $result->url = 'http://domain.com/thankyou.html';;
            $result->page = 'thankyou';
            $result->created_at = $date;
            $result->save();

        }

        Model::reguard();
    }

    private function truncate()
    {
        //truncate tables to start fresh
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach($this->toTruncate as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }


}
