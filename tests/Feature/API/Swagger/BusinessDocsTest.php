<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 24/01/19
 * Time: 5:41 PM
 */

namespace Tests\Feature\API\Swagger;


use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BusinessDocsTest extends TestCase
{
    public function testBusinessControllerStoreDocs()
    {
        $payload = File::get('storage/api-docs/api-docs.json');
        $payload_array = json_decode($payload, true);
        $paths = $payload_array['paths'];
        $busStore = $paths['/api/v1/businesses'];
        $post = $busStore['post'];

        $parameters = [
            ['name' => 'name', 'in' => 'query', 'description' => 'Name of business', 'required' => true,
                'schema' => ['type' => 'string']],
            ['name' => 'lat', 'in' => 'query', 'description' => 'Lat', 'required' => true,
                'schema' => ['type' => 'float']],
            ['name' => 'lng', 'in' => 'query', 'description' => 'Lng', 'required' => true,
                'schema' => ['type' => 'float']],
            ['name' => 'bio', 'in' => 'query', 'description' => 'Business bio', 'schema' => ['type' => 'string']],
            ['name' => 'avatar', 'in' => 'query', 'description' => 'Image encoded in base64',
                'schema' => ['type' => 'image']]
        ];

        $expected = [
            'operationId' => 'App\Http\Controllers\API\v1\BusinessesController::store',
            'parameters' => $parameters,
            'responses' => [
                200 => [ 'description' => 'BusinessResource'],
                400 => [ 'description' => 'Business not found']
            ]
        ];

        $this->assertEquals($expected, $post);
    }
}