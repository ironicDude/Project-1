<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;
    // protected static $singleTableType = 'applicant';
    // protected static $persisted = ['salary', 'personal_email', 'date_of_joining'];
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile',
        'status',
        'address',
        'resource',
    ];
    

        public function applicant_vacancy(){
            return $this->hasMany(Application::class);
            }
        public function applications(){
            return $this->hasMany(Application::class);
        }

}
