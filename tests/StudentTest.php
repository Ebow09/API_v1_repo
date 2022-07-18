<?php
namespace Tests;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Carbon\Carbon;

class StudentTest extends TestCase
{
    public function test_get_all_status_code(){           
        $this->get("api/v1/student", []);
        $this->seeStatusCode(200); 
    }
    
    public function test_get_all_data_expected_count(){
        //count number of results
        $this->get("api/v1/student", []);       
        $testArray = array(1, 2, 3, 4, 5, 6, 7, 8);  
        $expectedCount = 8;  
        $this->assertCount(
            $expectedCount,
            $testArray, "testArray contains 3 elements"
        );   
    }
    public function test_get_one_status_code(){
        $this->get("api/v1/showstudent/29", []);
        $this->seeStatusCode(200);

    }
    public function test_get_one_expected_count(){
        $this->get("api/v1/showstudent/29", []);
        $testArray = array(1);  
        $expectedCount = 1;  
        $this->assertCount(
            $expectedCount,
            $testArray, "testArray contains 1 element"
        );  
    }
    public function test_get_with_nondigit(){        

        $id = "s";
        $this->json('GET', '/api/v1/showstudent/'.$id,  [])
       ->seeJson([
            'message' => 'Error you must enter a valid id for this student',
       ]); 
   } 
    public function test_get_one_with_wrong_id(){
        $this->get("api/v1/showstudent/293", []);
        $testArray = array(1);  
        $expectedCount = 1;  
        $this->assertCount(
            $expectedCount,
            $testArray, "testArray does not contain any element"
        );  
    }
    public function test_get_one_with_no_id(){
        $this->get("api/v1/showstudent/", []);
        $this->seeStatusCode(404);
    }
    public function test_post_with_all_data(){
        
        $this->json('POST', '/api/v1/createstudent', 
        [   
            'firstname' => 'Amy',
            'surname' => 'Khan',
            'identificationno' => 'S12283',
            'country' => 'France',
            'dateofbirth' => '2012/05/09',
            'registeredon' => '2021/09/18 00:00:00'        
        ])
        ->seeJson([
            'message' => 'The record has been created',
        ]); 
    }
    public function test_post_with_missing_firstname_field(){
        //test field validation
        
        $this->json('POST', '/api/v1/createstudent', 
        [   
            //'firstname' => 'Amy',
            'surname' => 'Khan',
            'country' => 'France',
            'identificationno' => 'S12282',
            'dateofbirth' => '2012/05/09',
            'registeredon' => '2021/09/18 00:00:00'        
        ])
        ->seeJson([
            'firstname' => ['The firstname cannot be left blank.'],
        ]); 
    }
    public function test_post_with_missing_surname_field(){
        //test field validation
        
        $this->json('POST', '/api/v1/createstudent', 
        [   
            'firstname' => 'Amy',
            //'surname' => 'Khan',
            'country' => 'France',
            'identificationno' => 'S12288',
            'dateofbirth' => '2012/05/09',
            'registeredon' => '2021/09/18 00:00:00'        
        ])
        ->seeJson([
            'surname' => ['The surname cannot be left blank.'],
            
        ]); 
    }
    public function test_post_with_missing_country_field(){
        //test field validation
        
        $this->json('POST', '/api/v1/createstudent', 
        [   
            'firstname' => 'Amy',
            'surname' => 'Khan',
           // 'country' => 'France',
            'identificationno' => 'S12281',
            'dateofbirth' => '2012/05/09',
            'registeredon' => '2021/09/18 00:00:00'        
        ])
        ->seeJson([
            'country' => ['The country cannot be left blank.'],
        ]); 
    }
    public function test_post_with_missing_identificationno_field(){
        //test field validation
        
        $this->json('POST', '/api/v1/createstudent', 
        [   
            'firstname' => 'Amy',
            'surname' => 'Khan',
            'country' => 'France',
            //'identificationno' => 'S12241',
            'dateofbirth' => '2012/05/09',
            'registeredon' => '2021/09/18 00:00:00'        
        ])
        ->seeJson([
            'identificationno' => ['The identification number cannot be left blank.'],
        ]); 
    }
    public function test_post_with_missing_dateofbirth_field(){
        //test field validation
        
        $this->json('POST', '/api/v1/createstudent', 
        [   
            'firstname' => 'Amy',
            'surname' => 'Khan',
            'country' => 'France',
            'identificationno' => 'S12284',
            //'dateofbirth' => '2012/05/09',
            'registeredon' => '2021/09/18 00:00:00'        
        ])
        ->seeJson([
            'dateofbirth' => ['The date of birth cannot be left blank.'],
        ]); 
    }
    public function test_post_with_missing_registeredon_field(){
        //test field validation
        
        $this->json('POST', '/api/v1/createstudent', 
        [   
            'firstname' => 'Amy',
            'surname' => 'Khan',
            'country' => 'France',
            'identificationno' => 'S12285',
            'dateofbirth' => '2012/05/09',
            //'registeredon' => '2021/09/18 00:00:00'        
        ])
        ->seeJson([
            'registeredon' => ['The registered on date cannot be left blank.'],
        ]); 
    }
   
    public function test_post_identificationno_uniqueness_field(){
        //test field validation
        
        $this->json('POST', '/api/v1/createstudent', 
        [   
            'firstname' => 'Amy',
            'surname' => 'Khan',
            'country' => 'France',
            'identificationno' => 'S12240',
            'dateofbirth' => '2012/05/09',
            'registeredon' => '2021/09/18 00:00:00'        
        ])
        ->seeJson([
            "identificationno" => ["The identificationno has already been taken."],
        ]); 
    }
    public function test_post_with_invalid_dateofbirth_format(){
              
        $this->json('POST', '/api/v1/createstudent', 
        [   
            'firstname' => 'Tracy',
            'surname' => 'Beecham',
            'country' => 'USA',
            'identificationno' => 'S12286',
            'dateofbirth' => '2012er53',
            'registeredon' => '2021/09/18 00:00:00'        
        ])
        ->seeJson([
            'dateofbirth' => ['The dateofbirth is not a valid date.'],
        ]); 
    }
    public function test_post_with_invalid_registeredon_format(){
              
        $this->json('POST', '/api/v1/createstudent', 
        [   
            'firstname' => 'Tracy',
            'surname' => 'Beecham',
            'country' => 'USA',
            'identificationno' => 'S12290',
            'dateofbirth' => '2021-09-18',
            'registeredon' => '2023ert3'        
        ])
        ->seeJson([
            'registeredon' => ["The registeredon is not a valid date.","The registeredon must be a date after dateofbirth."],
        ]); 
    }

   
    public function test_update_all_valid_fields_with_correctid(){
        
        $this->json('PUT', '/api/v1/editstudent/14', 
         [   
            'firstname' => 'Amber',
            'surname' => 'Azaam',
            'country' => 'Netherland',
            'dateofbirth' => '2012/05/09',
            'registeredon' => '2021/09/18 00:00:00'        
        ])
        ->seeJson([
             'message' => 'The record has been edited',
        ]); 
    }  
    
    public function test_update_all_fields_with_incorrectid(){
        
        $this->json('PUT', '/api/v1/editstudent/11', 
         [   
            'firstname' => 'Beatrice',
            'surname' => 'Khan',
            'country' => 'Netherland',
            'dateofbirth' => '2012/05/09',
            'registeredon' => '2021/09/18 00:00:00'        
        ])
        ->seeJson([
             'message' => 'No student record was found for the given id',
        ]); 
    }  
    public function test_update_all_fields_with_missingid(){
   
        $this->put("api/v1/editstudent", []);
        $this->seeStatusCode(404); 

    }  
    public function test_update_with_missing_firstname(){       
        $this->json('PUT', '/api/v1/editstudent/11', 
         [              
            'surname' => 'Khan',
            'country' => 'Netherland',
            'dateofbirth' => '2012/05/09',
            'registeredon' => '2021/09/18 00:00:00'        
        ])
        ->seeJson([
             'firstname' => ["The firstname field is required."],
        ]);       
    }
    public function test_update_with_missing_surname(){       
        $this->json('PUT', '/api/v1/editstudent/11', 
         [   
            'firstname' => 'Beatrice',
            'country' => 'Netherland',
            'dateofbirth' => '2012/05/09',
            'registeredon' => '2021/09/18 00:00:00'        
        ])
        ->seeJson([
             'surname' => ["The surname field is required."],
        ]);       
    }
    public function test_update_with_missing_country(){       
        $this->json('PUT', '/api/v1/editstudent/11', 
         [   
            'firstname' => 'Beatrice',
            'surname' => 'Khan',
            'dateofbirth' => '2012/05/09',
            'registeredon' => '2021/09/18 00:00:00'        
        ])
        ->seeJson([
             'country' => ["The country field is required."],
        ]);       
    }
    public function test_update_with_missing_dateofbirth(){       
        $this->json('PUT', '/api/v1/editstudent/11', 
         [   
            'firstname' => 'Beatrice',
            'surname' => 'Khan',
            'country' => 'Netherland',
            'registeredon' => '2021/09/18 00:00:00'        
        ])
        ->seeJson([
             'dateofbirth' => ["The dateofbirth field is required."],
        ]);       
    }
    public function test_update_with_missing_registeredon(){       
        $this->json('PUT', '/api/v1/editstudent/11', 
         [   
            'firstname' => 'Beatrice',
            'surname' => 'Khan',
            'country' => 'Netherland',
            'dateofbirth' => '2012/05/09',
            //'registeredon' => '2021/09/18 00:00:00'        
        ])
        ->seeJson([
             'registeredon' => ["The registeredon field is required."],
        ]);       
    }
    public function test_update_with_registeredon_before_dateofbirth(){       
        $this->json('PUT', '/api/v1/editstudent/11', 
         [   
            'firstname' => 'Beatrice',
            'surname' => 'Khan',
            'country' => 'Netherland',
            'dateofbirth' => '2023/05/09',
            'registeredon' => '2021/09/18 00:00:00'        
        ])
        ->seeJson([
            "registeredon" => ["The registeredon must be a date after dateofbirth."],
        ]);       
    }
    public function test_update_with_registeredon_before_tomorrow(){      
        $tomorrow = Carbon::tomorrow();

        $this->json('PUT', '/api/v1/editstudent/14', 
         [   
            'firstname' => 'Beatrice',
            'surname' => 'Khan',
            'country' => 'Netherland',
            'dateofbirth' => '2013/05/09',
            'registeredon' => '2022/09/18 19:06:34'        
        ])
        ->seeJson([
            "registeredon" => ["The registeredon must be a date before ".$tomorrow."."],
        ]);       
    }
   
    public function test_update_with_invalid_dateofbirth_format(){      
        $tomorrow = Carbon::tomorrow();

        $this->json('PUT', '/api/v1/editstudent/14', 
         [   
            'firstname' => 'Beatrice',
            'surname' => 'Khan',
            'country' => 'Netherland',
            'dateofbirth' => '201305er',
            'registeredon' => '2022/09/18 19:06:34'        
        ])
        ->seeJson([
            "dateofbirth" => ["The dateofbirth is not a valid date."],
        ]);       
    }
    public function test_update_with_invalid_registeredon_format(){      
        $tomorrow = Carbon::tomorrow();

        $this->json('PUT', '/api/v1/editstudent/14', 
         [   
            'firstname' => 'Beatrice',
            'surname' => 'Khan',
            'country' => 'Netherland',
            'dateofbirth' => '2013/11/02',
            'registeredon' => '2022we34'        
        ])
        ->seeJson([
            "registeredon" => ["The registeredon is not a valid date.","The registeredon must be a date after dateofbirth."],
        ]);       
    }

    public function test_update_only_some_required_fields(){
       
        $this->put("api/v1/editstudent/14", []);
        $testArray = array("Yvette");  
        $expectedCount = 1;  
        $this->assertCount(
            $expectedCount,
            $testArray, "You need to inlcude all elements"
        );  
      
    }
    public function test_update_with_nondigit(){        

        $id = "s";
        $this->json('GET', '/api/v1/showstudent/'.$id,  [])
       ->seeJson([
            'message' => 'Error you must enter a valid id for this student',
       ]); 
   } 
    public function test_delete_with_missingid(){
   
        $this->delete("api/v1/deletestudent", []);
        $this->seeStatusCode(404); 

    }  
    public function test_delete_with_incorrectid(){        

         $id = intval(11);
         $this->json('DELETE', '/api/v1/deletestudent/'.$id,  [])
        ->seeJson([
             'message' => 'No student record was found for the given id: '. $id,
        ]); 
    } 

    public function test_delete_with_nondigit(){        

        $id = "s";
        $this->json('DELETE', '/api/v1/deletestudent/'.$id,  [])
       ->seeJson([
            'message' => 'Error you must enter a valid id for this student',
       ]); 
   } 
   public function test_delete_with_validId(){        

    $id = 28;
    $this->json('DELETE', '/api/v1/deletestudent/'.$id,  [])
   ->seeJson([
        'message' => 'The student record with '. $id. ' has been deleted',
   ]); 
   } 
   //Now uncomment line 22 in route/web.php and comment out line 23 to test authentication with Auth0
   public function test_post_with_no_authenticated_token()
   {
           //Set the token value in Auth0Middleware to NULL for this test  
           $this->json('POST', '/api/v1/createstudent', 
           [   
               'firstname' => 'Kofi',
               'surname' => 'Annan',
               'country' => 'Ghana',
               'identificationno' => 'S12293',
               'dateofbirth' => '2012-09-18',
               'registeredon' => '2021/09/18 00:00:00'        
           ])
           ->seeJson([
                   "No token provided",
           ]);
       
   }

   public function test_post_with_incorrect_authentication_token()   
   {         
       //Change token value in Auth0Middleware for this test  
       $this->json('POST', '/api/v1/createstudent', 
           [   
               'firstname' => 'Fredrica',
               'surname' => 'Nelson',
               'country' => 'Scotland',
               'identificationno' => 'S12294',
               'dateofbirth' => '2012-09-18',
               'registeredon' => '2021/09/18 00:00:00'        
           ])
           ->seeJson([
               "message" => "ID token could not be decoded",
           ]);        
   }

}