<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Role;
class Employee extends User
{
    use HasFactory;
    protected static $singleTableType = 'employee';
    protected static $persisted = ['salary', 'personal_email', 'date_of_joining', 'role_id'];
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'address',
        'date_of_birth',
        'gender_id',
        'image',
        'salary',
        'personal_email',
        'date_of_joining',
        'role_id'
    ];

    public function isAdministrator(){
        return $this->role->role == 'administrator';
    } //end of isAdministrator


    public function getSalary()
    {
        return $this->salary;
    }

    public function getPersonalEmail()
    {
        return $this->personal_email;
    }

    public function getDateOfJoining()
    {
        return $this->date_of_joining;
    }

    public function getRole()
    {
        return $this->role->role;
    }

    
    /**
     * Relationships
     */
    public function role(){
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'order_id', 'id');
    }
}
