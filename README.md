# Mentor API

A simple REST API for a professional social media network that allows users to apply as mentors for courses they have studied. Built with Laravel.

## Overview

This API allows:
- Users to register accounts
- Users to add courses they've studied to their profile
- Users to apply to become mentors for courses they've studied
- Applications to be approved or rejected
- Users to view approved mentors and their profiles
- Users to schedule with mentors via Calendly links

## Technical Implementation

- Uses in-memory mock repositories instead of a database for easy testing
- Follows SOLID principles, DRY, and KISS

## Setup Instructions

1. Clone the repository:
```bash
git clone https://github.com/Okonu/mentor-api.git
cd mentor-api
```

2. Install dependencies:
```bash
composer install
```

3. Copy environment file and set your app key:
```bash
cp .env.example .env
php artisan key:generate
```

4. Start the development server:
```bash
php artisan serve
```

## API Documentation

### Authentication

For simplicity, this implementation uses a header-based mock authentication.

1. **Register a new user** first (optional for testing as mock users are pre-populated):
   ```
   POST /api/register
   Content-Type: application/json
   
   {
     "name": "New User",
     "email": "newuser@example.com",
     "password": "password123"
   }
   ```

2. **For authenticated endpoints**, include the header `X-User-ID` with a user ID:
    - Use existing mock users: 1, 2, or 3
    - Or use the ID returned from registration

   Example:
   ```
   X-User-ID: 1
   ```

This approach simulates an authenticated request without implementing a complete authentication system.

### Available Endpoints

#### Authentication

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|--------------|
| POST | `/api/register` | Register a new user | No |

#### Courses

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|--------------|
| GET | `/api/courses` | List all courses | No |
| GET | `/api/courses/{id}` | Get a specific course | No |
| POST | `/api/courses` | Create a new course | Yes |

#### User-Course Association

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|--------------|
| GET | `/api/user/courses` | Get user's studied courses | Yes |
| POST | `/api/user/courses/{courseId}` | Add a course to user's studied courses | Yes |

#### Mentors

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|--------------|
| GET | `/api/mentors` | List all approved mentors | No |
| GET | `/api/mentors/{id}` | Get a specific mentor profile | No |
| POST | `/api/mentors/apply` | Apply to become a mentor | Yes |
| GET | `/api/user/mentor-status` | Get user's mentor status for all courses | Yes |

#### Applications

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|--------------|
| GET | `/api/applications` | List all mentor applications | Yes |
| GET | `/api/user/applications` | Get logged-in user's applications | Yes |
| PUT | `/api/applications/{id}/approve` | Approve an application | Yes |
| PUT | `/api/applications/{id}/reject` | Reject an application | Yes |

### Sample Requests for Each Endpoint

#### Authentication

##### Register a new user
```
POST /api/register
Headers:
  Content-Type: application/json
Body:
{
  "name": "Alex Johnson",
  "email": "alex@example.com",
  "password": "securepassword123"
}
```

#### Courses

##### List all courses
```
GET /api/courses
Headers:
  Accept: application/json
```

##### Get a specific course
```
GET /api/courses/1
Headers:
  Accept: application/json
```

##### Create a new course
```
POST /api/courses
Headers:
  Content-Type: application/json
  Accept: application/json
  X-User-ID: 1
Body:
{
  "name": "Python for Data Science",
  "description": "Learn Python programming and its applications in data analysis, visualization, and machine learning."
}
```

#### User-Course Association

##### Get user's studied courses
```
GET /api/user/courses
Headers:
  Accept: application/json
  X-User-ID: 1
```

##### Add course to user's studied courses
```
POST /api/user/courses/2
Headers:
  Accept: application/json
  X-User-ID: 1
```

#### Mentors

##### List all approved mentors
```
GET /api/mentors
Headers:
  Accept: application/json
```

##### Get a specific mentor profile
```
GET /api/mentors/1
Headers:
  Accept: application/json
```

##### Apply to become a mentor
```
POST /api/mentors/apply
Headers:
  Content-Type: application/json
  Accept: application/json
  X-User-ID: 1
Body:
{
  "course_id": 2,
  "calendly_link": "https://calendly.com/johndoe/data-structures-mentoring"
}
```

##### Get user's mentor status for all courses
```
GET /api/user/mentor-status
Headers:
  Accept: application/json
  X-User-ID: 1
```

#### Applications

##### List all applications
```
GET /api/applications
Headers:
  Accept: application/json
  X-User-ID: 3
```

##### Get logged-in user's applications
```
GET /api/user/applications
Headers:
  Accept: application/json
  X-User-ID: 1
```

##### Approve an application
```
PUT /api/applications/2/approve
Headers:
  Accept: application/json
  X-User-ID: 3
```

##### Reject an application
```
PUT /api/applications/2/reject
Headers:
  Content-Type: application/json
  Accept: application/json
  X-User-ID: 3
Body:
{
  "reason": "Insufficient expertise in this course"
}
```

## Mock Data

The API comes pre-populated with the following mock data:

### Users
```json
[
  {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "password": "[hashed]",
    "email_verified_at": "2025-02-27T00:00:00.000000Z"
  },
  {
    "id": 2,
    "name": "Jane Smith",
    "email": "jane@example.com",
    "password": "[hashed]",
    "email_verified_at": "2025-02-27T00:00:00.000000Z"
  },
  {
    "id": 3,
    "name": "Admin User",
    "email": "admin@example.com",
    "password": "[hashed]",
    "email_verified_at": "2025-02-27T00:00:00.000000Z"
  }
]
```

### Courses
```json
[
  {
    "id": 1,
    "name": "Computer Science 101",
    "description": "Introduction to Computer Science"
  },
  {
    "id": 2,
    "name": "Data Structures",
    "description": "Advanced data structures and algorithms"
  },
  {
    "id": 3,
    "name": "Web Development",
    "description": "Full-stack web development with modern frameworks"
  },
  {
    "id": 4,
    "name": "Machine Learning",
    "description": "Introduction to machine learning algorithms"
  }
]
```

### User-Course Associations
```
User 1 (John Doe) has studied: Courses 1, 3
User 2 (Jane Smith) has studied: Courses 2, 4
User 3 (Admin User) has studied: Courses 1, 2, 3, 4
```

### Pre-existing Applications
```json
[
  {
    "id": 1,
    "user_id": 1,
    "course_id": 1,
    "calendly_link": "https://calendly.com/johndoe/mentoring",
    "status": "approved",
    "rejection_reason": null
  },
  {
    "id": 2,
    "user_id": 2,
    "course_id": 2,
    "calendly_link": "https://calendly.com/janesmith/mentoring",
    "status": "pending",
    "rejection_reason": null
  },
  {
    "id": 3,
    "user_id": 1,
    "course_id": 3,
    "calendly_link": "https://calendly.com/johndoe/webdev-mentoring",
    "status": "rejected",
    "rejection_reason": "Insufficient experience in this field"
  }
]
```

## Testing the API

### Option 1: Using the Postman Collection
1. Import the included Postman collection `Mentor-API.postman_collection.json`
2. Set the environment variable `base_url` to `http://localhost:8000` (or your server URL)
3. The collection includes example requests for all endpoints
4. Make sure to include the `X-User-ID` header for authentication

### Option 2: Creating Your Own Requests
You can test the API without using the provided collection file:

1. Use Postman, cURL, or any API testing tool
2. Create requests using the endpoint documentation and sample data in this README
3. Always include the `X-User-ID` header (1, 2, or 3) to simulate authentication
4. For POST and PUT requests, set the Content-Type header to `application/json`

### Sample cURL Commands

#### List all courses
```bash
curl -X GET "http://localhost:8000/api/courses" -H "Accept: application/json"
```

#### Apply to be a mentor
```bash
curl -X POST "http://localhost:8000/api/mentors/apply" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-User-ID: 1" \
  -d '{"course_id": 2, "calendly_link": "https://calendly.com/johndoe/mentoring"}'
```

#### Register a new user
```bash
curl -X POST "http://localhost:8000/api/register" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name": "New User", "email": "newuser@example.com", "password": "password123"}'
```

## Expected API Responses

Below are examples of what to expect from the API:

### Course Listing Response
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Computer Science 101",
      "description": "Introduction to Computer Science",
      "created_at": "2025-01-19T00:00:00.000000Z",
      "updated_at": "2025-01-19T00:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "Data Structures",
      "description": "Advanced data structures and algorithms",
      "created_at": "2025-01-24T00:00:00.000000Z",
      "updated_at": "2025-01-24T00:00:00.000000Z"
    }
    // More courses...
  ]
}
```

### Mentor Application Response
```json
{
  "status": "success",
  "message": "Application submitted successfully",
  "data": {
    "id": 4,
    "user_id": 1,
    "course_id": 2,
    "calendly_link": "https://calendly.com/johndoe/data-structures-mentoring",
    "status": "pending",
    "created_at": "2025-02-27T12:34:56.000000Z",
    "updated_at": "2025-02-27T12:34:56.000000Z"
  }
}
```

### User Registration Response
```json
{
  "status": "success",
  "message": "User registered successfully.",
  "data": {
    "id": 4,
    "name": "Alex Johnson",
    "email": "alex@example.com"
  }
}
```

### Mentor List Response
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "mentored_courses": [
        {
          "id": 1,
          "name": "Computer Science 101",
          "description": "Introduction to Computer Science",
          "created_at": "2025-01-19T00:00:00.000000Z",
          "updated_at": "2025-01-19T00:00:00.000000Z",
          "calendly_link": "https://calendly.com/johndoe/mentoring"
        }
      ]
    }
  ]
}
```

### User Mentor Status Response
```json
{
  "status": "success",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "email_verified_at": "2025-02-27T00:00:00.000000Z",
      "created_at": "2025-01-28T00:00:00.000000Z",
      "updated_at": "2025-01-28T00:00:00.000000Z"
    },
    "courses": [
      {
        "id": 1,
        "name": "Computer Science 101",
        "description": "Introduction to Computer Science",
        "created_at": "2025-01-19T00:00:00.000000Z",
        "updated_at": "2025-01-19T00:00:00.000000Z",
        "mentor_status": "approved",
        "application": {
          "id": 1,
          "user_id": 1,
          "course_id": 1,
          "calendly_link": "https://calendly.com/johndoe/mentoring",
          "status": "approved",
          "rejection_reason": null,
          "created_at": "2025-02-07T00:00:00.000000Z",
          "updated_at": "2025-02-12T00:00:00.000000Z"
        }
      },
      {
        "id": 3,
        "name": "Web Development",
        "description": "Full-stack web development with modern frameworks",
        "created_at": "2025-01-29T00:00:00.000000Z",
        "updated_at": "2025-01-29T00:00:00.000000Z",
        "mentor_status": "rejected",
        "application": {
          "id": 3,
          "user_id": 1,
          "course_id": 3,
          "calendly_link": "https://calendly.com/johndoe/webdev-mentoring",
          "status": "rejected",
          "rejection_reason": "Insufficient experience in this field",
          "created_at": "2025-01-28T00:00:00.000000Z",
          "updated_at": "2025-02-02T00:00:00.000000Z"
        }
      }
    ]
  }
}
```

### Error Response Example
```json
{
  "status": "error",
  "errors": {
    "application": "You already have a pending mentor application"
  }
}
```

## Comprehensive Testing Guide

This section provides a step-by-step guide to testing each endpoint and understanding the expected behavior.

### 1. Authentication & User Registration

#### Register a new user
- **Endpoint**: POST `/api/register`
- **Test Case 1**: Register with valid data
    - Provide name, email, and password
    - Expected: 201 Created with user details
    - Purpose: Verify user creation works
- **Test Case 2**: Register with existing email
    - Use an email that already exists in mock data (e.g., "john@example.com")
    - Expected: 422 Unprocessable Entity with error message
    - Purpose: Verify duplicate email validation
- **Test Case 3**: Register with invalid data
    - Missing required fields or invalid email format
    - Expected: 422 Unprocessable Entity with validation errors
    - Purpose: Verify input validation

### 2. Course Management

#### List all courses
- **Endpoint**: GET `/api/courses`
- **Test Case**: Basic request
    - No special parameters needed
    - Expected: 200 OK with array of 4 courses
    - Purpose: Verify course listing works

#### Get a specific course
- **Endpoint**: GET `/api/courses/{id}`
- **Test Case 1**: Valid course ID
    - Use ID 1-4 from mock data
    - Expected: 200 OK with course details
    - Purpose: Verify single course retrieval
- **Test Case 2**: Invalid course ID
    - Use ID that doesn't exist (e.g., 99)
    - Expected: 404 Not Found with error message
    - Purpose: Verify proper error handling

#### Create a new course
- **Endpoint**: POST `/api/courses`
- **Test Case 1**: Create with valid data (authenticated)
    - Include name and description with X-User-ID header
    - Expected: 201 Created with course details
    - Purpose: Verify course creation works
- **Test Case 2**: Create without authentication
    - Omit the X-User-ID header
    - Expected: 401 Unauthorized
    - Purpose: Verify authentication protection
- **Test Case 3**: Create with invalid data
    - Missing required fields
    - Expected: 422 Unprocessable Entity with validation errors
    - Purpose: Verify input validation

### 3. User-Course Association

#### Get user's studied courses
- **Endpoint**: GET `/api/user/courses`
- **Test Case 1**: Authenticated request
    - Include X-User-ID header for an existing user
    - Expected: 200 OK with array of courses
    - Purpose: Verify user's courses are returned
- **Test Case 2**: Request without authentication
    - Omit the X-User-ID header
    - Expected: 401 Unauthorized
    - Purpose: Verify authentication protection

#### Add course to user's studied courses
- **Endpoint**: POST `/api/user/courses/{courseId}`
- **Test Case 1**: Add new course (authenticated)
    - Use a course ID that the user hasn't studied yet
    - Expected: 200 OK with success message
    - Purpose: Verify course association works
- **Test Case 2**: Add course that user already has
    - Use a course ID that is already in the user's studied courses
    - Expected: 422 Unprocessable Entity with error message
    - Purpose: Verify duplicate prevention
- **Test Case 3**: Add non-existent course
    - Use a course ID that doesn't exist
    - Expected: 404 Not Found with error message
    - Purpose: Verify error handling

### 4. Mentor Application

#### Apply to become a mentor
- **Endpoint**: POST `/api/mentors/apply`
- **Test Case 1**: Valid application (authenticated)
    - Provide course_id and calendly_link for a course the user has studied
    - Expected: 201 Created with application details
    - Purpose: Verify application submission works
- **Test Case 2**: Apply for course user hasn't studied
    - Provide course_id for a course not in user's study history
    - Expected: 422 Unprocessable Entity with error
    - Purpose: Verify business rule enforcement
- **Test Case 3**: Apply with existing pending application
    - Make a second application request after a successful first one
    - Expected: 422 Unprocessable Entity with error about pending application
    - Purpose: Verify duplicate prevention

#### Get user mentor status
- **Endpoint**: GET `/api/user/mentor-status`
- **Test Case**: Authenticated request
    - Include X-User-ID header
    - Expected: 200 OK with user's courses and mentor status for each
    - Purpose: Verify status retrieval works

### 5. Application Management

#### Get all applications
- **Endpoint**: GET `/api/applications`
- **Test Case**: Authenticated request
    - Include X-User-ID header
    - Expected: 200 OK with array of all applications
    - Purpose: Verify application listing works

#### Get user's applications
- **Endpoint**: GET `/api/user/applications`
- **Test Case**: Authenticated request
    - Include X-User-ID header
    - Expected: 200 OK with array of user's applications
    - Purpose: Verify user-specific application listing works

#### Approve an application
- **Endpoint**: PUT `/api/applications/{id}/approve`
- **Test Case 1**: Approve pending application
    - Use ID of a pending application (e.g., ID 2)
    - Expected: 200 OK with updated application status
    - Purpose: Verify approval process works
- **Test Case 2**: Approve already approved/rejected application
    - Use ID of non-pending application
    - Expected: 422 Unprocessable Entity with error message
    - Purpose: Verify state validation

#### Reject an application
- **Endpoint**: PUT `/api/applications/{id}/reject`
- **Test Case 1**: Reject pending application with reason
    - Provide rejection reason for a pending application
    - Expected: 200 OK with updated application status
    - Purpose: Verify rejection process works
- **Test Case 2**: Reject non-pending application
    - Use ID of non-pending application
    - Expected: 422 Unprocessable Entity with error message
    - Purpose: Verify state validation

### 6. Mentor Listing

#### List all mentors
- **Endpoint**: GET `/api/mentors`
- **Test Case**: Basic request
    - No special parameters needed
    - Expected: 200 OK with array of approved mentors
    - Purpose: Verify mentor listing works

#### Get mentor profile
- **Endpoint**: GET `/api/mentors/{id}`
- **Test Case 1**: View existing mentor
    - Use ID of user with approved application (e.g., ID 1)
    - Expected: 200 OK with mentor profile including mentored courses
    - Purpose: Verify mentor profile retrieval
- **Test Case 2**: View non-mentor user
    - Use ID of user without approved applications
    - Expected: 404 Not Found with error message
    - Purpose: Verify proper error handling

### Testing Sequence

To fully test the application flow, follow this sequence:

1. Register a new user (or use an existing one)
2. View available courses
3. Add a course to the user's studied courses
4. Apply to become a mentor for that course
5. Check the user's mentor status (should be "pending")
6. View the pending application in the applications list
7. Approve the application
8. Check the user's mentor status again (should be "approved")
9. View the user in the mentors list
10. View the mentor's profile

This sequence tests the complete lifecycle of a mentor application and validates the core functionality of the API.

## Architecture

```
app/
├── Http/
│   ├── Controllers/
│   │   └── API/
│   │       ├── ApplicationController.php
│   │       ├── AuthController.php
│   │       ├── CourseController.php
│   │       ├── MentorController.php
│   │       └── UserCourseController.php
│   └── Middleware/
│       └── MockAuthMiddleware.php
├── Models/
│   ├── Course.php
│   ├── MentorApplication.php
│   └── User.php
├── Providers/
│   └── RepositoryServiceProvider.php
├── Repositories/
│   ├── Interfaces/
│   │   ├── CourseRepositoryInterface.php
│   │   ├── MentorApplicationRepositoryInterface.php
│   │   └── UserRepositoryInterface.php
│   └── Mock/
│       ├── CourseRepository.php
│       ├── MentorApplicationRepository.php
│       ├── MockRepository.php
│       └── UserRepository.php
└── Services/
    └── MentorService.php
```

## Implementation Details

### Repository Pattern
The API uses the Repository pattern with in-memory data stores to simulate a database:

- **MockRepository**: Base repository with CRUD operations
- **UserRepository**: Manages user data and course associations
- **CourseRepository**: Manages course data
- **MentorApplicationRepository**: Handles mentor applications

### Service Layer
A service layer handles business logic:

- **MentorService**: Contains all mentor-related business rules

### Controllers
Controllers handle HTTP requests and delegate to services:

- **AuthController**: User registration
- **CourseController**: Course management
- **UserCourseController**: User-course associations
- **MentorController**: Mentor operations
- **ApplicationController**: Application management

### Authentication
Simple middleware-based authentication using the X-User-ID header.

## Notes on Implementation

- Mock repositories use in-memory collections for simplicity
- The code follows SOLID principles and clean architecture
- The API can be easily extended to use a real database by implementing alternate repository classes
