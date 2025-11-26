<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
class ExternalEmployees extends Authenticatable 
{
    protected $table = "external_employees";

    protected $fillable = ['username', 'password', 'first_name', 'last_name']; 

    protected $hidden = ['password', 'remember_token'];

  public function getNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function fullName(){
        return $this->first_name . " " . $this->last_name;
    }


}
