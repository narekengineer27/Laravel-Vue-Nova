<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 24/01/19
 * Time: 2:45 AM
 */

namespace Tests\Unit\Models;


use App\Models\BusinessPost;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testBusinessPostRelationRoundTrip()
    {
        $post = factory(BusinessPost::class)->create();
        $user = User::findOrFail($post->user_id);

        $nuUser = $post->user()->first();
        $this->assertTrue($nuUser instanceof User);
        $this->assertEquals($user->getKey(), $nuUser->getKey());
        $nuPost = $user->businessPosts()->first();
        $this->assertTrue($nuPost instanceof BusinessPost);
        $this->assertEquals($post->getKey(), $nuPost->getKey());
    }
}
