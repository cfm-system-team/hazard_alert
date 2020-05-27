<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Recipient
 *
 * @property int $id
 * @property int $group_id
 * @property string $email
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Recipient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Recipient newQuery()
 * @method static Builder|Recipient onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Recipient query()
 * @method static \Illuminate\Database\Eloquent\Builder|Recipient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipient whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipient whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipient whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recipient whereUpdatedAt($value)
 * @method static Builder|Recipient withTrashed()
 * @method static Builder|Recipient withoutTrashed()
 * @mixin Eloquent
 * @property string|null $agreed_at
 * @method static \Illuminate\Database\Eloquent\Builder|Recipient whereAgreedAt($value)
 */
class Recipient extends Model
{
    use SoftDeletes;

    /**
     * @see Model::$fillable
     */
    protected $fillable = [
        'email',
        'group_id',
        'agreed_at'
    ];

    /**
     * @see Model::$casts
     */
    protected $casts = [
        'id' => 'integer',
        'group_id' => 'integer'
    ];
}
