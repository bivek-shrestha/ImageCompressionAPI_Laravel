# Laravel API for File/Image Upload Using Queues

## Overview
This project is a Laravel-based API that allows users to upload images/files, which are then processed asynchronously using Laravel Queues. The API reduces image quality to 80% before storage using the GD package for image processing.

## Installation & Setup

### Steps

1. Create a new Laravel project:
   ```bash
   composer create-project laravel/laravel Image_Upload_API
   ```
2. Navigate to the project directory:
   ```bash
   cd Image_Upload_API
   ```
3. Ensure the GD Library is enabled in PHP.
4. Configure the `.env` file for database and queue connection.
5. Run database migrations:
   ```bash
   php artisan migrate
   ```
6. Set up queue driver in `.env`:
   ```env
   QUEUE_CONNECTION=database
   ```
7. Start the queue worker:
   ```bash
   php artisan queue:work
   ```

## API Endpoint Details

| Method | Endpoint      | Description              | Parameters                        |
|--------|--------------|--------------------------|-----------------------------------|
| POST   | /api/upload  | Upload an image to queue | `file` (required, image file)     |

## Code Components

### **Route Definition**
```php
Route::post('/upload', [ImageUploadController::class, 'upload']);
```
Defines a POST route at `/upload`, which calls the `upload` method of `ImageUploadController`.

### **Controller: `ImageUploadController.php`**
#### Upload Function
```php
public function upload(Request $request)
```
Handles image upload, validates the file, and dispatches it to the queue.

#### Validation
```php
$validator = Validator::make($request->all(), [
    'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
]);
```
Ensures the uploaded file is an image and meets size/mime requirements.

#### Storing Image
```php
$path = $request->file('image')->store('uploads', 'public');
```
Saves the uploaded image in the `uploads` directory on the public disk.

#### JSON Response
```php
return response()->json([
    'message' => 'Image uploaded successfully',
    'path'    => $path
], 200);
```
Returns a success response with the uploaded image path.

### **Queue Job: `ProcessImageUpload.php`**

#### Constructor
```php
public function __construct($filePath)
{
    $this->filePath = $filePath;
}
```
Used for class initialization.

#### Handle Function
```php
public function handle()
```
Processes the image file, checks its type, and compresses it.

#### File Path Handling
```php
$storagePath = storage_path('app/public/' . $this->filePath);
if (!file_exists($storagePath)) {
    \Log::error('File not found at path: ' . $storagePath);
    return;
}
```
Constructs the path and checks if the file exists for processing.

#### Image Processing
##### JPEG Processing
```php
$image = imagecreatefromjpeg($storagePath);
if ($image) {
    $result = imagejpeg($image, $storagePath, 80);
}
```
Compresses JPEG files to 80% quality.

##### PNG Processing
```php
$image = imagecreatefrompng($storagePath);
if ($image) {
    $result = imagepng($image, $storagePath, 8);
}
```
Compresses PNG files with quality level 8.

##### GIF Processing
```php
$image = imagecreatefromgif($storagePath);
if ($image) {
    $result = imagegif($image, $storagePath);
}
```
Processes and saves GIF files.

## Queue Configuration

| Configuration        | Command                    |
|----------------------|---------------------------|
| Run queue worker    | `php artisan queue:work`  |

## Storage Configuration

| Path                           | Description                    |
|--------------------------------|--------------------------------|
| `storage/app/public/uploads/` | Directory where images are stored |
| Run storage link               | `php artisan storage:link`      |

## Testing the API

| Tool    | Command                                        |
|---------|-----------------------------------------------|
| Postman | Send a POST request with an image file to `/api/upload` |

### Expected Response

| Status Code | Message                                     |
|------------|---------------------------------|
| 200        | `{ "message": "Image uploaded successfully." }` |
| 422        | `{ "error": "Invalid file type." }`           |

## Error Handling

| Error Type         | Solution                                                   |
|--------------------|------------------------------------------------------------|
| Queue Not Running | Ensure `php artisan queue:work` is running and restart it after changes. |

## API Testing Output

### Screenshots
- **Postman API Testing**  
  _[Insert Screenshot Here]_  
- **Initial Property of Image**  
  _[Insert Screenshot Here]_  
- **Converted Property of Image**  
  _[Insert Screenshot Here]_  

## References

| Reference Name    | Link/Description                                     |
|------------------|----------------------------------------------------|
| Laravel Docs     | [Laravel Documentation](https://laravel.com/docs/) |
| GD Image Library | [GD Image Processing](https://www.binarytides.com/compress-images-php-using-gd/) |
| Laravel Queues   | [Laravel Queue Docs](https://laravel.com/docs/queues) |
| Postman Docs     | [Postman Documentation](https://learning.postman.com/docs/) |

---

### Done and Submitted by:  
**Bivek Shrestha**  
📞 9816270779  
📧 bivekshrestha00@gmail.com

