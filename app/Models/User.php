<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Http\Resources\AllergyCollection;
use App\Http\Resources\AllergyResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Gender;
use App\Models\AccountStatus;
use Nanigans\SingleTableInheritance\SingleTableInheritanceTrait;
use App\Models\Employee;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use SingleTableInheritanceTrait;
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected static $singleTableTypeField = 'type';
    protected static $singleTableSubclasses = [Employee::class, Customer::class];
    protected static $persisted = [
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'remember_token',
        'mobile',
        'password',
        'address',
        'date_of_birth',
        'type',
        'gender_id',
        'image',
        'account_status_id'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'address',
        'type',
        'date_of_birth',
        'user_role_id',
        'gender_id',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Deactivate the user's account by changing the 'account_status_id' to "Blocked".
     *
     * This method updates the 'account_status_id' attribute of the current user model to the ID of the "Blocked" status
     * in the 'account_statuses' table. It then saves the updated user model to persist the changes in the database.
     *
     * @return void
     */
    public function deactivate(): void
    {
        // Retrieve the 'id' value for the "Blocked" status from the 'account_statuses' table.
        $blockedStatusId = AccountStatus::where('status', 'Blocked')->value('id');

        // Update the 'account_status_id' attribute of the current user model to the "Blocked" status ID.
        $this->account_status_id = $blockedStatusId;

        // Save the updated user model to persist the changes in the database.
        $this->save();
    }

    /**
     * Activate the user's account by changing the 'account_status_id' to "Active".
     *
     * This method updates the 'account_status_id' attribute of the current user model to the ID of the "Active" status
     * in the 'account_statuses' table. It then saves the updated user model to persist the changes in the database.
     *
     * @return void
     */
    public function activate(): void
    {
        // Retrieve the 'id' value for the "Active" status from the 'account_statuses' table.
        $activeStatusId = AccountStatus::where('status', 'Active')->value('id');

        // Update the 'account_status_id' attribute of the current user model to the "Active" status ID.
        $this->account_status_id = $activeStatusId;

        // Save the updated user model to persist the changes in the database.
        $this->save();
    }

    /**
     * Check if the user is directly allergic to a given product.
     *
     * This method checks if the user is directly allergic to the provided product.
     * It verifies whether the product exists in the collection of the user's allergies.
     *
     * @param \App\Models\Product $product The product to check for allergy.
     * @return bool Returns true if the user is directly allergic to the product, otherwise false.
     */
    public function isAllergicTo(Product $product)
    {
        return $this->allergies->contains($product);
    }

    /**
     * Check if the user is indirectly allergic to a given product.
     *
     * This method checks if the user is indirectly allergic to the provided product.
     * It retrieves all the products associated with the drugs of the user's allergies,
     * then excludes the products that are directly allergic to the user.
     * Finally, it checks if the provided product exists in the remaining collection.
     *
     * @param \App\Models\Product $product The product to check for indirect allergy.
     * @return bool Returns true if the user is indirectly allergic to the product, otherwise false.
     */
    public function isIndirectlyAllergicTo(Product $product)
    {
        // Get all products associated with the drugs of the user's allergies.
        $allergyDrugProducts = $this->allergies->pluck('drug.products')->flatten();

        // Exclude the products that are directly allergic to the user.
        $allergyDrugProducts = $allergyDrugProducts->diff($this->allergies);

        // Check if the provided product exists in the remaining collection.
        return $allergyDrugProducts->contains($product);
    }


    /**
     * Check if the authenticated user is a customer
     *
     * @return bool Returns true if the user is a customer and false if not.
     */
    public static function isCustomer($user)
    {
        return $user->type == 'customer'
        ? true
        : false;
    }

    public function getAllergies()
    {
        $allergies = $this->allergies;
        return new AllergyCollection($allergies);
    }


    public function getFirstName()
    {
        return $this->first_name;
    }

    public function getLastName()
    {
        return $this->last_name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getMobile()
    {
        return $this->mobile;
    }

    public function getGender()
    {
        return $this->gender->gender;
    }

    public function getDateOfBirth()
    {
        return $this->date_of_birth;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getAccountStatus()
    {
        return $this->accountStatus->status;
    }

     /**
     * Get the address of the authenticated user.
     *
     * @return string
     */
    public function getAddress()
    {
        return Auth::user()->address;
    }

    public function setFirstName(string $firstName)
    {
        $this->first_name = $firstName;
        $this->save();
        return $this->first_name;
    }

    public function setLastName(string $lastName)
    {
        $this->last_name = $lastName;
        $this->save();
        return $this->last_name;
    }

    public function setMobile(string $lastName)
    {
        $this->last_name = $lastName;
        $this->save();
        return $this->last_name;
    }

    public function setGender(string $gender)
    {
        $this->gender = Gender::where('gender', $gender)->first();
        $this->save();
        return $this->gender->gender;
    }

    public function setDateOfBirth(Carbon $date)
    {
        $this->gender = Gender::where('gender', $date)->first();
        $this->save();
        return $this->date_of_birth;
    }


    /**
     * relationships
     */
    public function gender()
    {
        return $this->belongsTo(Gender::class, 'gender_id', 'id');
    }
    public function accountStatus()
    {
        return $this->belongsTo(AccountStatus::class, 'account_status_id', 'id');
    }
    public function ratings()
    {
        return $this->belongsToMany(User::class, 'ratings', 'product_id', 'user_id')->withPivot('rating');
    }
    public function allergies()
    {
        return $this->belongsToMany(Product::class, 'allergies', 'product_id', 'user_id');
    }
}
