# Mentor API

A simple REST API for a professional social media network that allows users to apply as mentors for courses they have studied.

## Overview

This API allows:
- Users to apply to become mentors for courses they've studied
- Applications to be approved or rejected
- Users to view approved mentors and their profiles
- Users to schedule with mentors via Calendly links

## Technical Implementation

- Built with Laravel
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
Include the header `X-User-ID` with a user ID (1, 2, or 3 for the mock users) to simulate an authenticated request.

### Available Endpoints

#### Courses

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/courses` | List all courses |
| GET | `/api/courses/{id}` | Get a specific course |

#### Mentors

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/mentors` | List all approved mentors |
| GET | `/api/mentors/{id}` | Get a specific mentor profile |
| POST | `/api/mentors/apply` | Apply to become a mentor |

#### Applications

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/applications` | List all mentor applications |
| GET | `/api/user/applications` | Get logged-in user's applications |
| PUT | `/api/applications/{id}/approve` | Approve an application |
| PUT | `/api/applications/{id}/reject` | Reject an application |

### Sample Requests

#### Apply to become a mentor

```
POST /api/mentors/apply
Headers:
  Content-Type: application/json
  X-User-ID: 1
Body:
{
  "course_id": 2,
  "calendly_link": "https://calendly.com/johndoe/data-structures-mentoring"
}
```

#### Approve an application

```
PUT /api/applications/2/approve
Headers:
  Content-Type: application/json
  X-User-ID: 3 (admin user)
```

#### Reject an application

```
PUT /api/applications/2/reject
Headers:
  Content-Type: application/json
  X-User-ID: 3 (admin user)
Body:
{
  "reason": "Insufficient expertise in this course"
}
```

## Testing the API

### Option 1: Using the Postman Collection
1. Import the included Postman collection `Mentor-API.postman_collection.json`
2. The collection includes example requests for all endpoints
3. Make sure to include the `X-User-ID` header for authentication

### Option 2: Creating Your Own Requests
You can test the API without using the provided collection file:

1. Use Postman, cURL, or any API testing tool
2. Create requests using the endpoint documentation in this README
3. Always include the `X-User-ID` header (1, 2, or 3) to simulate authentication
4. For POST and PUT requests, set the Content-Type header to `application/json`

## Mock Data

The API comes pre-populated with the following mock data:

### Users
- ID: 1, Name: John Doe, Email: john@example.com
- ID: 2, Name: Jane Smith, Email: jane@example.com
- ID: 3, Name: Admin User, Email: admin@example.com

### Courses
- ID: 1, Name: Computer Science 101
- ID: 2, Name: Data Structures
- ID: 3, Name: Web Development
- ID: 4, Name: Machine Learning

### Pre-existing Applications
- John Doe is an approved mentor for Computer Science 101
- Jane Smith has a pending application for Data Structures
- John Doe was rejected for Web Development

## Notes on Implementation

- The implementation uses the Repository pattern to separate data access concerns
- A service layer handles business logic
- Controllers are kept lean and focus on HTTP concerns
- Mock repositories use in-memory collections rather than a database
- The application could be easily switched to use a real database by implementing alternate repository classes

## Architecture

```
app/
├── Http/
│   └── Controllers/
│       └── API/
│           ├── ApplicationController.php
│           ├── CourseController.php
│           └── MentorController.php
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
