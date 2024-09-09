<?php

namespace App\Imports;

use App\Mail\UserRegistered;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class UsersImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * Define validation rules for the import.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'full_name'    => 'string|max:255',
            'email'        => 'email|max:255|unique:users,email',
            'ic_number'    => 'digits_between:6,12', // Ensure IC number has between 6 to 12 digits
            'gender'       => 'string|max:255',
            'nationality'  => 'string|max:255',
            'race'         => 'string|max:255',
            'org_guid'     => 'exists:organizations,org_name',
            'position'     => 'string|max:255',
            'role_guid'    => 'exists:roles,role_name',
        ];
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */



    public function model(array $row)
    {
        // Ensure the keys exist in the row array
        $expectedKeys = ['full_name', 'email', 'ic_number', 'gender', 'nationality', 'race', 'org_guid', 'position', 'role_guid'];

        foreach ($expectedKeys as $key) {
            if (!array_key_exists($key, $row)) {
                return null; // Skip the row or handle it as needed
            }
        }

        // Generate a random password
        $generatedPassword = Str::random(10);
        $uuid = (string) Str::uuid();

        // Lookup the company by name (org_guid)
        $organization = Organization::where('org_name', $row['org_guid'])->first();

        // Lookup the role by name (role_guid)
        $role = Role::where('role_name', $row['role_guid'])->first();

        // Handle missing organization or role
        if (!$organization || !$role) {
            return null;
        }

        // Return a new User instance
        $user = User::create([
            'full_name'    => $row['full_name'],
            'email'        => $row['email'],
            'ic_number'    => $row['ic_number'],
            'gender'       => $row['gender'],
            'nationality'  => $row['nationality'],
            'race'         => $row['race'],
            'org_guid'     => $organization->id,    // Use the organization ID
            'position'     => $row['position'],
            'role_guid'    => $role->id,            // Use the role ID
            'password'     => Hash::make($generatedPassword), // Hash the password
            'is_active'    => 'Y',                  // Mark the user as active
            'uuid'         => $uuid,                // Unique identifier for the user
        ]);

        // Dispatch the email job to the queue
        Mail::to($user->email)
            ->later(30, new UserRegistered($user, $generatedPassword)); // Using queue

        return $user;
    }
}
