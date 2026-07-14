<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnologyMaster extends Model
{
    protected $table = 'Technology_Master';
    protected $primaryKey = 'Technology_id';
    public $timestamps = false;
    protected $fillable = ['Technology_Name'];
}
