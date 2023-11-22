<?php
namespace App;

use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

use App\Models\Seller;

/**
 * Class User
 *
 * @package App
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
*/
class User extends Authenticatable
{
    use HasFactory, HasApiTokens, HasRoles, SoftDeletes, CascadeSoftDeletes, Notifiable;

	protected $cascadeDeletes = ['seller'];

	protected $fillable = [
		'name',
		'lastname',
		'email',
		'password',
		'status',
		'phone',
		'picture',
		'remember_token',
		'active',
	];

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = ['id'];
    protected $guard_name = 'web';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['roles'];

    /**
     * Hash password
     * @param $input
     */
    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    /**
     * The roles that belong to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function role()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

	/**
     * The Seller that belong to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
	public function seller()
	{
		return $this->hasOne(Seller::class);
	}

    /**
     * The Sales that belong to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sales()
    {
        return $this->belongsToMany(Sale::class)
                    ->using(Seller::class)
                    ->as('Seller')
                    ->withPivot('id')
                    ->withTimestamps();
    }

	public function getFullname()
	{
		return $this->name . ' ' . $this->lastname;
	}
}
