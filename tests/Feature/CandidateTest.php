<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CandidateTest extends TestCase
{
    use WithFaker;

    public function testDenyNonHrdToGetList()
    {
        $response = $this->get('/api/candidate');
        $response->assertStatus(401);
    }

    public function testSuccessfullyGetList()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->get('/api/candidate');
        $response->assertStatus(200);

        $user->delete();
    }

    public function testDenyNonHrdToGetDataPerId()
    {
        $candidate = Candidate::factory()->create();
    
        $response = $this->get('/api/candidate/' . $candidate->id);
        $response->assertStatus(401);
        
        $candidate->delete();
    }

    public function testSuccessfullyGetDataPerId()
    {
        $user = User::factory()->create();
        $candidate = Candidate::factory()->create();
 
        $response = $this->actingAs($user, 'api')->get('/api/candidate/' . $candidate->id);
        $response->assertStatus(200);

        $user->delete();
        $candidate->delete();
    }

    public function testDenyNonSeniorHrdToCreate()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->post('/api/candidate', $this->candidateData());
        $response->assertStatus(403);

        $user->delete();
    }

    public function testSeniorHrdSuccessfullyCreate()
    {
        $user = User::factory()->state(['is_senior' => true])->create();

        $response = $this->actingAs($user, 'api')->post('/api/candidate', $this->candidateData());
        $response->assertStatus(201);

        $user->delete();
    }

    /**
     * @dataProvider ValidationDataProvider
     */
    public function testCreateValidation(array $invalidData)
    {
        $user = User::factory()->state(['is_senior' => true])->create();

        $data = array_merge($this->candidateData(), $invalidData);

        $response = $this->actingAs($user, 'api')->post('/api/candidate', $data);
        $response->assertStatus(422);

        $user->delete();
    }

    public function testDenyNonSeniorHrdToUpdate()
    {
        $user = User::factory()->create();
        $candidate = Candidate::factory()->create();

        $response = $this->actingAs($user, 'api')->post('/api/candidate/' . $candidate->id, $this->candidateData());
        $response->assertStatus(403);

        $user->delete();
        $candidate->delete();
    }

    public function testSeniorHrdSuccessfullyUpdate()
    {
        $user = User::factory()->state(['is_senior' => true])->create();
        $candidate = Candidate::factory()->create();

        $response = $this->actingAs($user, 'api')->post('/api/candidate/' . $candidate->id, $this->candidateData());
        $response->assertStatus(200);

        $user->delete();
        $candidate->delete();
    }

    /**
     * @dataProvider ValidationDataProvider
     */
    public function testUpdateValidation(array $invalidData)
    {
        $user = User::factory()->state(['is_senior' => true])->create();
        $candidate = Candidate::factory()->create();

        $data = array_merge($this->candidateData(), $invalidData);

        $response = $this->actingAs($user, 'api')->post('/api/candidate/' . $candidate->id, $data);
        $response->assertStatus(422);
        
        $user->delete();
    }

    public function testDenyNonSeniorHrdToDelete()
    {
        $user = User::factory()->create();
        $candidate = Candidate::factory()->create();
        
        $response = $this->actingAs($user)->delete('/api/candidate/' . $candidate->id);
        $response->assertStatus(403);
    }

    public function testNotFoundDelete()
    {
        $user = User::factory()->state(['is_senior' => true])->create();
        
        $response = $this->actingAs($user, 'api')->delete('/api/candidate/' . 999);
        $response->assertStatus(404);
    }

    public function testSeniorHrdSuccessfullyDelete()
    {
        $user = User::factory()->state(['is_senior' => true])->create();
        $candidate = Candidate::factory()->create();
        
        $response = $this->actingAs($user, 'api')->delete('/api/candidate/' . $candidate->id);
        $response->assertStatus(204);
    }

    public function candidateData()
    {
        return [
            'name' => $this->faker->name,
            'education' => $this->faker->sentence,
            'birthday' => now(),
            'experience' => $this->faker->sentence,
            'last_position' => $this->faker->sentence,
            'applied_position' => $this->faker->sentence,
            'top_5_skills' => $this->faker->sentence,
            'email' => $this->faker->unique()->email,
            'phone' => $this->faker->randomNumber(),
            'resume' => UploadedFile::fake()->create('test.pdf')
        ];
    }

    public function ValidationDataProvider()
    {
        return [
            [['name' => ''], 'name'],
            [['name' => null], 'name'],
            [['name' => []], 'name'],
            [['education' => ''], 'education'],
            [['education' => null], 'education'],
            [['birthday' => ''], 'birthday'],
            [['birthday' => null], 'birthday'],
            [['birthday' => 'one'], 'birthday'],
            [['experience' => ''], 'experience'],
            [['experience' => null], 'experience'],
            [['last_position' => ''], 'last_position'],
            [['last_position' => null], 'last_position'],
            [['applied_position' => ''], 'applied_position'],
            [['applied_position' => null], 'applied_position'],
            [['top_5_skills' => ''], 'top_5_skills'],
            [['top_5_skills' => null], 'top_5_skills'],
            [['email' => ''], 'email'],
            [['email' => null], 'email'],
            [['email' => 'email'], 'email'],
            [['phone' => ''], 'phone'],
            [['phone' => null], 'phone'],
            [['phone' => 'test'], 'phone'],
            [['resume' => ''], 'resume'],
            [['resume' => null], 'resume'],
            [['resume' => 'test'], 'resume'],
            [['resume' => 'test.jpg'], 'resume'],
        ];
    }

}
