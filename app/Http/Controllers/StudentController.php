<?php
namespace App\Http\Controllers;
//use App\Models\Student;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\PDO;
use Carbon\Carbon;

class StudentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
   
    public function __construct( )
    {
        //Use PDO for data manipulation
        $db = DB::connection()->getPdo();
        $this->db =$db;
    }
    
     public function index()
     {
     

        $sql = 'SELECT * FROM student';

        $stmt = $this->db->query($sql);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return response()->json([
                  'student' => $results,
        ], 200);

     }

     public function create(Request $request)
     {
        $tomorrow = Carbon::tomorrow();
        //validate inputs
        $this->validate($request, [
            'firstname' => 'required',
            'surname' => 'required',
            'identificationno' => 'required|unique:student',
            'country' => 'required|alpha',
            'dateofbirth' => 'date|required| before:' . Carbon::now()->subYears(3)->format('Y-m-d'),
            'registeredon' => 'date|required|after:dateofbirth|before:' .  $tomorrow 
        ],
           [ 'firstname.required' => 'The firstname cannot be left blank.',
           'surname.required' => 'The surname cannot be left blank.',
           'identificationno.required' => 'The identification number cannot be left blank.',
           'country.required' => 'The country cannot be left blank.' , 
           'dateofbirth.required' => 'The date of birth cannot be left blank.', 
           'registeredon.required' => 'The registered on date cannot be left blank.' 
        ]);
        //You could change the number in the subYears function to a suitable value based on their year of admission
        
        $firstname = $request -> firstname;
        $surname = $request -> surname;
        $identificationno = $request -> identificationno;
        $country = $request -> country;
        $dateofbirth = $request -> dateofbirth;
        $registeredon = $request -> registeredon;

        $sql = "INSERT INTO student (firstname, surname, identificationno, country, dateofbirth, registeredon )
         VALUES (?, ?, ?, ?, ?, ?)";

            $stmt= $this->db->prepare($sql);
            if($stmt->execute([$firstname, $surname, $identificationno, $country, $dateofbirth, $registeredon]))
            {
                
                $message = 'The record has been created';
                if ( $stmt->rowCount() == 0 ){
                    $message = 'The record was not inserted. Is the identification number unique?';  
                }
                return response() -> json(['message' => $message]);
            } else {
                return response()->json([
                    'message' => 'Oops!! The record could not be saved, check your data and try again',
                ]);
            }
     }

     public function show($id)
     {
        if(!is_numeric($id)){
            return response() -> json(['message' => 'Error you must enter a valid id for this student']);
            exit();
        } 
       
        $stmt = $this->db ->prepare('SELECT * FROM student WHERE id = ?');
        $stmt->execute([$id]);

        if ($id = $stmt->fetch(\PDO::FETCH_ASSOC)){           
            return response()->json([
                'firstname' => $id['firstname'],
                'surname' => $id['surname'],
                'identificationno' => $id['identificationno'],
                'country' => $id['country'],
                'dateofbirth' => $id['dateofbirth'],
                'registeredon' => $id['registeredon'],
            ], 200);
        }
        return response()->json([
            'message' => 'The given id does not match any student in the database',
        ], 401);
       
     }

     public function update(Request $request)
     { 
        $tomorrow = Carbon::tomorrow();
        //validate inputs - identification number will not be edited because it is unique for this record
        $this->validate($request, [
            'firstname' => 'required',
            'surname' => 'required',
            'country' => 'required',
            'dateofbirth' => 'date|required| before:' . Carbon::now()->subYears(3)->format('Y-m-d'),
            'registeredon' => 'date|required|after:dateofbirth|before:' .  $tomorrow 
        ]);
        //Get values
        $id = $request -> id;
        if(!is_numeric($id)){
            return response() -> json(['message' => 'Error you must enter a valid id for this student']);
            exit();
        } 
        $firstname = $request -> firstname;
        $surname = $request -> surname;
        $country = $request -> country;
        $dateofbirth = $request -> dateofbirth;
        $registeredon = $request -> registeredon;
      
        $sql = "UPDATE student SET firstname = ? , surname  = ?,  country  = ?, 
        dateofbirth  = ?, registeredon  = ?  WHERE id = ? ";

           $stmt= $this->db->prepare($sql);
           if($stmt->execute([$firstname, $surname, $country, $dateofbirth, $registeredon, $id]))
           {
               $message = 'The record has been edited'; 
               if ( $stmt->rowCount() == 0 ){
                   $message = 'No student record was found for the given id';  
               } 
               return response() -> json(['message' => $message]);
           } else {
               return response()->json([
                   'message' => 'Oops!! The student record could not be updated!',
               ]);
           }         
     }

     public function destroy($id)
     {      
        if(!is_numeric($id)){
            return response() -> json(['message' => 'Error you must enter a valid id for this student']);
            exit();
        } 
           
        $sql = "DELETE FROM student WHERE id = ? ";

           $stmt= $this->db->prepare($sql);
           if($stmt->execute([$id]))
           {
               $message = 'The student record with '. $id. ' has been deleted'; 
               if ( $stmt->rowCount() == 0 ){
                   $message = 'No student record was found for the given id: '. $id;  
               } 
               return response() -> json(['message' => $message]);
           } else {
               return response()->json([
                   'message' => 'Oops!! The student record could not be deleted!',
               ]);
           }  
     }
   
}