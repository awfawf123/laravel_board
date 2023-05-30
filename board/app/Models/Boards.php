<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Boards extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = ['id','created_at']; // 블랙리스트방식 id,created_at 값 수정을 안하려고
    protected $dates = ['deleted_at']; //delete flg를 자동적으로 해줄려고 use SoftDelete
}
