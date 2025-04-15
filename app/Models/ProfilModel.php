<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilModel extends Model
{
    use HasFactory;

    protected $table = 'm_profil';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = ['user_id', 'foto'];


    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
}
