<?php

namespace App;

use App\Notifications\VerifyEmail;
use App\Role;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use function foo\func;

/**
 * App\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Question\Question[] $questions
 * @property-read int|null $questions_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable;

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected
        $guarded = ['id'];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected
        $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected
        $casts = [
        'email_verified_at' => 'datetime',
    ];


//  JWT: Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public
    function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public
    function getJWTCustomClaims()
    {
        return [];
    }


    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail); // my notification
    }


    /**
     * RELATIONSHIPS WITH OTHER MODELS
     *
     *
     *
     *
     *  One To One
     */
    public
    function company()
    {
        return $this->hasOne(Company::class);
    }

    public
    function resume()
    {
        return $this->hasOne(Resume::class);
    }

    public
    function timeline()
    {
        return $this->belongsTo(Timeline::class);
    }


    /**
     *   One To Many
     */


    public
    function country()
    {
        return $this->belongsTo(Country::class);
    }

    public
    function province()
    {
        return $this->belongsTo(Province::class);
    }

    public
    function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     *  Many To One
     */
    public
    function questions()
    {
        return $this->hasMany(Question::class);
    }


    public
    function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public
    function posts()
    {
        return $this->hasMany(Post::class);
    }

    public
    function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public
    function userExperiences()
    {
        return $this->hasMany(UserExperience::class);
    }


    public
    function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     *  Many To Many
     */
    public
    function likes()
    {
        return $this->belongsToMany(Post::class, 'likes', 'user_id', 'post_id')->withTimestamps();
    }

    public
    function shares()
    {
        return $this->belongsToMany(Post::class, 'shares', 'user_id', 'post_id')->withTimestamps();
    }

//    company follow relationships
    public
    function companies()
    {
        return $this->belongsToMany(Company::class, 'company_follow', 'user_id', 'company_id')->withTimestamps();
    }


    public
    function request_senders()
    {
        return $this->belongsToMany(User::class, 'friendships', 'recipient_id', 'sender_id')->withPivot('status')->withTimestamps();
    }


    public
    function request_recipients()
    {
        return $this->belongsToMany(User::class, 'friendships', 'sender_id', 'recipient_id')->withPivot('status')->withTimestamps();
    }

    public
    function groups()
    {
        return $this->belongsToMany(Group::class)->withPivot('status');
    }

    public
    function myGroups()
    {
        return $this->hasMany(Group::class, 'admin_id');
    }

    public
    function hashtags()
    {
        return $this->belongsToMany(Hashtag::class);
    }


    /**
     * Events
     */
    public
    static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            //
        });



        static::deleted(function ($user) {

//            if ($user->company) {
//                $user->company->jobs()->delete();
//                $user->company->delete();
//            }
//
//            if ($user->resume) {
//                $user->resume->delete();
//            }
//
//            $user->posts()->delete();
//            $user->comments()->delete();
//            $user->myGroups()->delete();
//            $user->userExperiences()->delete();
//            $user->contacts()->delete();
//
//
//
//            $user->contacts()->delete();
//
//            Session::where('user1_id', $user->id)->delete();
//            Session::where('user2_id', $user->id)->delete();

//            $user->request_senders()->detach();
//            $user->request_recipients()->detach();

        });
    }


    /**
     * @param $input
     * @return bool
     */
    public
    function hasRole($input)
    {
        if (is_array($input)) {
            foreach ($input as $role) {
                if ($role === $this->role->name) {
                    return true;
                }
            }
        }
        if ($this->role->name === $input) {
            return true;
        }
        return false;
    }


}
