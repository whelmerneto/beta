<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property float $balance
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Account extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'balance'];
    protected $connection = 'mysql';
    protected $table = 'accounts';

}
