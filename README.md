# Requirements

Docker

# Install

(install script working on ubuntu 18.x)
```bash
apt update
apt-get install docker -y
apt-get install docker.io -y
apt-get install docker-compose -y
apt-get install nginx -y
sysctl -w vm.max_map_count=262144 #<--very important if you run on a VM for elastic to work!

cd /root
mkdir api
cd api
git clone https://repo/ .
git checkout staging

vi config/app.php #change APP_URL


chmod -R +x ./build-deploy/
docker-compose up -d
	#-> may have to run /etc/init.d/nginx stop incase port 80 already in use

vi .env #change APP_URL

docker-compose ps # <---check all running

docker container exec -it app bash
composer install
composer dumpautoload
head /etc/nginx/nginx.conf # to find out the web user (may be www-data)
chown -R nginx:nginx .
chmod -R 777 storage
chmod -Rf 777 /var/www/app/storage/logs
chmod -Rf 777 /var/www/app/storage/framework/views/
chmod -Rf 777 /var/www/app/storage/framework/sessions/
chmod -Rf 777 /var/www/app/storage/framework/cache/
sh ./build-deploy/first-run.sh
```

TEST YOUR SETUP IS WORKING:
* you can access http://localhost/dashboard
* you can login to the dashboard (admin PW is in seeders file: database/seeds/StaffUsersSeeder.php)
* goto business page--->result: there are items on the map (this comes from elastic)


# Run

To start the project, just run:

```bash
docker-compose up
```

If you want to start containers in background (as a daemon), add the `-d` flag:

```bash
docker-compose up -d
```

If this is your first run, then after containers are up and ready, run the next command to setup/install the project dependencies:

```bash
docker-compose run --rm app ./build-deploy/first-run.sh
```

# Stop

You can stop containers by typing `Cmd + C` on Mac or `Ctrl + C` on Windows/Linux. 

If you started the project in background, then run:

```bash
docker-compose stop
```
# Force Running Project

There are many unexpected problems to stop running project. ex. Low version Ubuntu, OpenVPN connect problem.
```bash
sudo service docker restart
docker-compose down --rmi=local -v -f
```
This command will stop docker and restart, also remove all docker containers.
And Follow above steps again. Important : 1.if you use vpn tool, disable it. 2. Maintain Memory 4GB: Run ElasticSearch docker Container.

# Cleanup

If you want to cleanup your Docker instances for a fresh start, run:

```bash
docker-compose down --rmi=local -v
```

This command will stop and delete the containers, local images and volumes.

# Updating external images

If you want to get latest versions of your external images, run:

```bash
docker-compose pull
```

# Queues

Once your containers are started, you can start the queue listeners when needed.

### Mailing Queue

```bash
docker-compose run --rm app php artisan queue:work sqs_mail
```

### SMS Queue

```bash
docker-compose run --rm app php artisan queue:work sqs_sms
```

# Tests

To run the test suite:
```bash
docker-compose run --rm app ./build-deploy/test.sh
```

# ElasticSearch Indexes

To regenerate the elastic search indexes:
```bash
docker-compose run --rm app php artisan elastic:setup-indexes
```

# Mac Set-up

```Docker Mac

    1. Install the docker in your mac

    2. After installing docker in your mac clone the repo (app) into your machine 

    3. Configure .env file and change mysql db_host to 127.0.0.1 and also change in docker.yaml file. mysql Environment to `MYSQL_HOST=127.0.0.1` and `app: aliases: - 127.0.0.1`

    4. Inside your app run `composer commands` to update all packages

    5. after that run make sure you have the righ permissions for that
            go to app->build-deploy
                run `sudo chmod  +x first_run.sh`
                run `sudo chmod  +x run.sh`
            next go to app->build-deploy->image
                run `sudo chmod  +x run.sh`

    6. Now run  `docker-compose build` and  inside your app

    7. Start Laravel server to access app

    If you use sequel pro for mysql use these credentials database: app, host: 127.0.0.1, username: root
    Now you are able to access the app `http://127.0.0.1:8000/dashboard:login` url

    ```
#################################################
#         SWAGGER USING DOCUMENTATION           #
#################################################

# Generate beautiful RESTful Laravel API documentation with Swagger
- Run: php artisan list  TO check swagger already integrated in application:
If swagger already integrated, it will show these items in artisan list
l5-swagger
  l5-swagger:generate        Regenerate docs
  l5-swagger:publish         Publish config, views

- Create an endpoint and automate the documentation:
In controller class, above method of controller which you want to make API documentation, add below lines in comment:
/**
     * @OA\Post/Get/Delete/Put(
     *     path="/api/v1/{method_route_url}",
     *     summary="Example summary.",
     *     @OA\Parameter(
     *         list all parameters for method
     *         required=true,
     *         in="query"
     *     ),
     *     @OA\Response(
     *         write response format here
     *     )
     * )
     */


- RUN: php artisan l5-swagger:generate to generate document

- API document should be here: {app_url}/api/documentation


#################################################
#         DEBUGGING								#
#################################################

################################################
# ISSUE INSTALLING `composer install`
################################################
  Problem 1
    - laravel/nova 1.0.x-dev requires babenkoivan/scout-elasticsearch-driver v3.8.2 -> satisfiable by babenkoivan/scout-elasticsearch-driver[v3.8.2].
    - laravel/nova dev-master requires babenkoivan/scout-elasticsearch-driver v3.8.2 -> satisfiable by babenkoivan/scout-elasticsearch-driver[v3.8.2].
    - Conclusion: don't install babenkoivan/scout-elasticsearch-driver v3.8.2
    - Installation request for laravel/nova * -> satisfiable by laravel/nova[1.0.x-dev, dev-master].

FIX:
remove the babenkoivan package from composer json - it is pulled in as a direct dependency of nova package automatically with a correct version
################################################


===================================
GENERAL DEBUG
===================================
tail -f storage/logs/laravel.log





#################################################
#         HELPFUL / NOTES						#
#################################################

===================================
#To run all of your outstanding migrations, execute the migrate Artisan command:
php artisan migrate
===================================
DOCS URL
/api/documentation

ENABLE: L5_SWAGGER_GENERATE_ALWAYS in: config/l5-swagger.php to always regenerate (NOT FOR PROD)
===================================
===================================
RESET KIBANA PW
===================================
docker container exec -it api bash
htpasswd /etc/nginx/domains.d/kibana.htpasswd kibana
===================================
RESET A PASSWORD
===================================
cd <your_larave_project_directory_path>
php artisan tinker
Psy Shell v0.4.4 (PHP 5.5.28 — cli) by Justin Hileman
>>>
$user = User::where('email', 'admin@test.com')->first();
$user->password = Hash::make('d0asnas08s43');
$user->save();
===================================

===================================
IMAGES NOT LOADIN IN ADMIN?
config/app.php - change APP_URL
===================================




==============OLD NOTES============
==============OLD NOTES============
==============OLD NOTES============
==============OLD NOTES============
API controllers:

API\Ownership folder
- ConfirmController - handles confirmation of ownership (user_id owning a business)
- MethodsController - methods of claiming ownership for J. Random Business
- RequestsController - handles requests for business ownership - claiming?

API folder
- ActiveBusinessPostsController - get list of unexpired posts for J. Random Business
- BusinessBioController - generate generic bio for J. Random Business, update with supplied bio
- BusinessCoverController - upload post photo for J. Random Business
- BusinessesController - business CRUD, stats for searching businesses images and reviews in supplied area, business avatar UD
- BusinessFeedController - feed of reviews and posts for J. Random Business
- BusinessHoursController - update opening hours for J. Random Business
- BusinessPostsController - post CRU (no D) for J. Random Business
- BusinessReviewsController - create reviews of J. Random Business
- BusinessSearchController - handle elastic search queries for/on businesses
- CategoriesController - category CRUD
- DiscoverController - get list of businesses similar to J. Random Business
- ExploreController - location-limited business query search
- FaceDetectionController - detect faces from supplied image
- FeedController - List of businesses within certain distance of supplied point (2 km?),
- LoginController - Handle API-side login/logout
- MapPresetController - List of active map presets
- RegisterController - Handle user registrations, with (apparently-optional) 2FA
- StickerCategoriesController - List of sticker categories
- StickersController - List of stickers, either all, within a given category, and/or matching at least one of supplied tags
- TopCategoriesSearchController - List of popular categories
- UserBusinessesController - CD of user-business relations
- UserCategoriesController - List and C of user-category relations
- UserFeedController - Shuffled list of current user's reviews and businesses
- UserOptionalAttributesController - CRUD of optional, possibly user-defined, business attributes.
- UsersController - Update user details.

Services:
- BusinessBioService - generate default bio for J. Random Business, get closest city to J. Random Business
- BusinessCategoryService - return highest-ranking categories that match a supplied query.
- BusinessService - search for businesses near supplied point matching query, matching category (if supplied or implied by supplied map preset).
- CategoryService - get active categories list.
- FeedService - Return list of feeds near supplied point, XOR for J. Random Business, XOR for current user.
- MapPresetService - Return paginated list of active map presets.

Repositories:
- BusinessAttributesRepository - get list of attributes for J. Random Business in supplied list of keys.
- BusinessRepository - handle generic (from location, supplied query, category), similiar (to J. Random Business), find-specific, and suggest (from supplied query) queries for businesses.
- CategoriesRepository - Return specific category or all of them.
- FeedRepository - Return list of feeds near supplied point, XOR for J. Random Business, XOR for current user.
- MapPresetsRepository - Return specific map preset or query builder.
- OwnershipRequestsRepository - Check whether J. Random User has active request, check whether J. Random User has active request for J. Random Business, confirm request (no apparent check for expiry or previous confirm), create new request.
- OwnershipsRepository - create new active ownerships  (user-business combo is PK on ownerships table).

Models:

Authenticatable:
User - J. Random User.  Can only access API bitz.
StaffUser - Site Staff.  Need to be this to access Nova bitz.  Does not appear able to access API bitz.

Users can request PW resets but StaffUsers cannot - few enough to do manually?

Business-related:
Business - Name, lat, lng, bio, uuid, owner_id
BusinessAttribute - freeform key-value pairs to further describe parent business
BusinessCategory - Pivot model between business and category, with additional relevance score.
BusinessHour - Track open/close times of parent business by day-of-week
BusinessKeyword - Tracking keywords applicable to parent business, with additional relevance score.  Should this be a pivot model?
BusinessOptionalAttribute - Pivot model between business and optional attribute.
BusinessPost - Expirable site posts by parent business.
BusinessPostImage - image record for parent business post.
BusinessPostImageLabel - unsure. Is this still being used?
BusinessReview - recording reviews of business by user, with comment, code (rating?) and metadata.
BusinessReviewExtra - json metadata for parent business review.
BusinessReviewImage - image record for parent business review.  Points to image storage on S3.
BusinessReviewKeyword - looks very similar to BusinessKeyword, with same additional data.  Should also be a pivot model?

Map-related:
MapPreset - Some sort of map preset for mapping choice, eg Google maps?
MapPresetBusinessHour - analogue of BusinessHour for MapPreset
MapPresetCategory - Pivot table between map present and category, with only timestamps extra.
MapPresetHour - analogue of BusinessHour for MapPreset - is this a duplicate of MapPresetBusinessHour?

Ownership - pivot table between business and user that owns it
OwnershipRequest - request to take ownership of given business record

StickerCategory - category of stickers
Sticker - sticker/decal image record pointing to image storage presumably on S3.

Category - category to store both users and businesses.
Label - key/value pair.  Hangs off BusinessAttribute.

DB Refactoring questions:
I did note an apparently complete lack of polymorphic relations among the models.  Are polymorphic relations being avoided
because it breaks (I think) first and third normal form, because no one was comfortable with them, or something else?

BusinessKeyword and BusinessReviewKeyword both look like they really want to be pivot models, pointing to a "Keyword" model.
(Category currently does the same for both User and Business)

Assuming that polymorphic relations are OK to use, here's what I noticed:

BusinessHour and MapPresetBusinessHour - can they be refactored into a polymorphic many-to-many relation, to an "OpenHours" model or the like?
{$FOO}Image - can they be similarly refactored into a polymorphic M:N relation, to an "Image" model?
BusinessKeyword and BusinessReviewKeyword - can they be similarly refactored into a polymorphic M:N relation, to Keyword model with pivot data?

DB questions:
Why do the BusinessHour models have a boolean field for each day?  There seems to be no mechanism ensuring that
at most one record for J. Random Business' opening hours on a given day-of-week exists.  There did seem to be
such a day-of-week field in the past - why did it get removed?

Inconsistent relations:
I noticed that the following relations were inconsistent - for example differing pivot fields:
Business->categories() has relevance pivot and timestamps, but Category->businesses() only has timestamps.
Business->optionalAttributes() has description pivot and timestamps, but OptionalAttribute->businesses() only has timestamps.

Dangling relations:
BusinessAttribute->label() points to Label, but there's no reciprocal relation.
BusinessPost->images() points to BusinessPostImage, but no reciprocal relation.
BusinessReview->images() points to BusinessReviewImage, but no reciprocal relation.

Relations that need looking at:
User->bookmarks() is doing.. what?  It doesn't look like user_bookmarks was ever in migrations.
Business->hours() and Business->businessHours() have common stem
Business->addyAttributes(), Business->emailAttributes() has common stem with Business->attributes()
Since Ownerships is a glorified pivot table, shouldn't it have belongsTo relations with its parent Business, User and OwnershipRequest?

