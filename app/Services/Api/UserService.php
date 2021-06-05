<?php

namespace App\Services\Api;

use App\Models\User;
use App\Utils\ImageUploadService;
use Illuminate\Http\UploadedFile;

class UserService
{
    /**
     * @var User
     */
    private $model;

    /**
     * @var ImageUploadService
     */
    private $imageUploadService;

    /**
     * UserService constructor.
     * @param User $user
     * @param ImageUploadService $imageUploadService
     */
    public function __construct(User $user, ImageUploadService $imageUploadService)
    {
        $this->model = $user;
        $this->imageUploadService = $imageUploadService;
    }

    /**
     * Gets all the users from the DB.
     * @return User[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function getCurrent()
    {
        return auth()->user();
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getUserReviews($userId, $perPage)
    {
        return $this->model->where('uuid', $userId)->first()->reviews()->with('business')->latest()->paginate($perPage);
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getUserReviewsCount($userId)
    {
        return $this->model->where('uuid', $userId)->reviews()->count();
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getBookmarks($user)
    {
        $user = $this->resolveUser($user);
        return $user->bookmarks;
    }

    /**
     * Gets a single user from DB based on provided ID
     * @param $id
     * @return User
     */
    public function get($id)
    {
        return $this->model->findOrFail($id);
    }


    /**
     * Create a user from a request
     * @param array $data
     * @return User
     * @throws \App\Exceptions\GeneralException
     */
    public function create(array $data)
    {
        $data['password'] = bcrypt($data['password']);
        $data['cover_photo'] = $this->processImage($data, 'cover_photo');
        $data['avatar_photo'] = $this->processImage($data, 'avatar_photo');
        return $this->model->create($data);
    }

    /**
     * Update a user based on provided ID
     * @param $user
     * @param array $data
     * @return User
     * @throws \App\Exceptions\GeneralException
     */
    public function update($user, array $data)
    {
        if (!empty($data['cover_photo'])) {
            $data['cover_photo'] = $this->processImage($data, 'cover_photo');
        }
        if (!empty($data['avatar_photo'])) {
            $data['avatar_photo'] = $this->processImage($data, 'avatar_photo');
        }
        $user = $this->resolveUser($user);
        $user->fill($data)->save();
        return $user;
    }

    /**
     * @param mixed $user
     * @return bool|null
     * @throws \Exception
     */
    public function delete($user)
    {
        return $this->resolveUser($user)->delete();
    }

    /**
     * Return a user based on supplied id
     * Or if instance of user is returned, do nothing
     * @param $user
     * @return User
     */
    private function resolveUser($user)
    {
        if ($user instanceof User) {
            return $user;
        }
        return $this->get($user);
    }

    /**
     * @param $data
     * @param $key
     * @return string|null
     * @throws \App\Exceptions\GeneralException
     */
    private function processImage($data, $key)
    {
        if (empty($data[$key])) {
            return null;
        }
        if ($data[$key] instanceof UploadedFile) {
            return $this->imageUploadService->saveImage($data[$key], 'images');
        }
        return null;
    }


}
