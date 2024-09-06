<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class UsersImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    protected $orgGuid;
    protected $roleGuid;

    public function __construct($orgGuid, $roleGuid)
    {
        $this->orgGuid = $orgGuid;
        $this->roleGuid = $roleGuid;
    }

    public function model(array $row)
    {
        $generatedPassword = Str::random(10);

        return new User([
            'full_name' => $row['full_name'],
            'email' => $row['email'],
            'ic_number' => $row['ic_number'],
            'nationality' => $row['nationality'],
            'gender' => $row['gender'],
            'position' => $row['position'],
            'org_guid' => $this->orgGuid,
            'role_guid' => $this->roleGuid,
            'race' => $row['race'],
            'password' => Hash::make($generatedPassword),
            'is_active' => 'Y',
        ]);
    }

    public function rules(): array
    {
        return [
            '*.full_name' => 'required|string|max:255',
            '*.email' => 'required|email|unique:users,email',
            '*.ic_number' => 'required|digits_between:6,12',
            '*.nationality' => 'required|string|max:255',
            '*.gender' => 'required|string|max:255',
            '*.position' => 'required|string|max:255',
            // '*.role_guid' => 'required',
            // '*.org_guid' => 'required',
            '*.race' => 'required|string|max:255',
        ];
    }
}
