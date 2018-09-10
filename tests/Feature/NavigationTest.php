<?php

namespace Tests\Feature;

use App\Campaign;
use App\Institution;
use App\Result;
use App\Staff;
use App\Student;
use App\User;

class NavigationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testPages()
    {
        $admin = User::where('type','admin')->first();
        $manager = User::where('type','institution')->first();
        $recruiter = User::where('type','staff')->first();
        $institution = Institution::where('user_id',$manager->id)->first();
        $campaign = Campaign::where('institution_id',$institution->id)->first();
        $staff = Staff::where('institution_id',$institution->id)->first();
        $result = Result::where('campaign_id',$campaign->id)->first();
        $student = Student::find($result->student_id);

        //Admin
        $this->actingAs($admin)
            ->get('/institutions')
            ->assertSee($institution->name);

        //Manager
        $this->actingAs($manager)
            ->withSession(['institution' => $institution])
            ->get('/campaigns')
            ->assertSee($campaign->name);

        $this->get('/settings/profile')
            ->assertSee($manager->email);

        $this->withSession(['campaign' => $campaign])
            ->get('/campaign/settings')
            ->assertSee($campaign->name);

        $this->get('/campaign/students')
            ->assertSee($student->firstName.' '.$student->lastName);

        $this->get('/campaign/results')
            ->assertSee('google.com');

        $this->get('/campaign/students/'.$student->id)
            ->assertSee($student->firstName.' '.$student->lastName);

        $this->get('/staff')
            ->assertSee($staff->name);

        $this->get('/staff/'.$staff->id)
            ->assertSee($staff->name);

        //Recruiter
        $this->actingAs($recruiter)
            ->withSession(['institution' => $institution])
            ->get('/campaigns')
            ->assertSee($campaign->name);

        $this->withSession(['campaign' => $campaign])
            ->get('/campaign/students')
            ->assertSee($student->firstName.' '.$student->lastName)
            ->assertDontSeeText('Results');

    }
}
