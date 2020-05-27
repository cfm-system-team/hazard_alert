<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Group
 *
 * @mixin Builder
 * @property int $id
 * @property string $name
 * @property string $zip_code
 * @property string $address
 * @property string|null $start_at
 * @property string|null $end_at
 * @property string $owner
 * @property string $telephone
 * @property string $hash
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection|Recipient[] $recipients
 * @property-read int|null $recipients_count
 * @method static Builder|Group newModelQuery()
 * @method static Builder|Group newQuery()
 * @method static \Illuminate\Database\Query\Builder|Group onlyTrashed()
 * @method static Builder|Group query()
 * @method static Builder|Group whereAddress($value)
 * @method static Builder|Group whereCreatedAt($value)
 * @method static Builder|Group whereDeletedAt($value)
 * @method static Builder|Group whereEndAt($value)
 * @method static Builder|Group whereHash($value)
 * @method static Builder|Group whereId($value)
 * @method static Builder|Group whereName($value)
 * @method static Builder|Group whereOwner($value)
 * @method static Builder|Group whereStartAt($value)
 * @method static Builder|Group whereTelephone($value)
 * @method static Builder|Group whereUpdatedAt($value)
 * @method static Builder|Group whereZipCode($value)
 * @method static \Illuminate\Database\Query\Builder|Group withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Group withoutTrashed()
 * @property string|null $agreed_at
 * @property string $email
 * @method static Builder|Group whereAgreedAt($value)
 * @method static Builder|Group whereEmail($value)
 */
class Group extends Model
{
    use SoftDeletes;

    /**
     * @see Model::$fillable
     */
    protected $fillable = [
        'name',
        'owner',
        'telephone',
        'email',
        'zip_code',
        'address',
        'start_at',
        'end_at',
        'hash',
        'agreed_at'
    ];

    /**
     * @see Model::$casts
     */
    protected $casts = [
        'id' => 'integer'
    ];

    /**
     * recipientsテーブルとのリレーション。
     *
     * @return HasMany
     */
    public function recipients()
    {
        return $this->hasMany(Recipient::class);
    }
}
