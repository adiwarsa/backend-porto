# Project API Documentation

## Base URL
```
http://your-domain.com/api
```

## Authentication
All endpoints require API token authentication via the `api.token` middleware.

## Endpoints

### 1. Get All Projects
**GET** `/projects`

**Query Parameters:**
- `type` (optional): Filter projects by type (e.g., `website`, `mobile`, `desktop`)
- `status` (optional): Filter projects by status (e.g., `Active Development`, `Completed`)
- `technology` (optional): Filter projects by technology (e.g., `React`, `Laravel`)

**Examples:**
- `GET /projects` - Get all projects
- `GET /projects?type=website` - Get only website projects
- `GET /projects?status=Completed` - Get only completed projects
- `GET /projects?technology=React` - Get projects using React
- `GET /projects?type=website&status=Completed` - Get completed website projects

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "title": "Project Title",
            "type": "website",
            "images": [
                "http://your-domain.com/storage/projects/image1.jpg",
                "http://your-domain.com/storage/projects/image2.jpg"
            ],
            "author": "Adi Warsa",
            "date": "2024-01-01",
            "description": "Project description",
            "technologies": ["React", "Laravel", "MySQL"],
            "features": ["Feature 1", "Feature 2"],
            "status": "Active Development",
            "liveUrl": "https://example.com",
            "githubUrl": "https://github.com/username/repo",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    ],
    "meta": {
        "total": 1,
        "message": "Projects retrieved successfully",
        "portfolio_info": {
            "total_projects": 1,
            "technologies_used": ["React", "Laravel", "MySQL"],
            "project_types": ["website"],
            "status_distribution": {
                "Active Development": 1
            }
        }
    }
}
```

### 2. Get Single Project
**GET** `/projects/{id}`

**Response:**
```json
{
    "data": {
        "id": 1,
        "title": "Project Title",
        "type": "website",
        "images": [
            "http://your-domain.com/storage/projects/image1.jpg",
            "http://your-domain.com/storage/projects/image2.jpg"
        ],
        "author": "Adi Warsa",
        "date": "2024-01-01",
        "description": "Project description",
        "technologies": ["React", "Laravel", "MySQL"],
        "features": ["Feature 1", "Feature 2"],
        "status": "Active Development",
        "liveUrl": "https://example.com",
        "githubUrl": "https://github.com/username/repo",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

### 3. Create Project
**POST** `/projects`

**Request Body:**
```json
{
    "title": "New Project",
    "type": "website",
    "images": ["image1.jpg", "image2.jpg"],
    "author": "Adi Warsa",
    "date": "2024-01-01",
    "description": "Project description",
    "technologies": ["React", "Laravel"],
    "features": ["Feature 1", "Feature 2"],
    "status": "Active Development",
    "liveUrl": "https://example.com",
    "githubUrl": "https://github.com/username/repo"
}
```

**Response:** (Status: 201)
```json
{
    "data": {
        "id": 2,
        "title": "New Project",
        "type": "website",
        "images": [
            "http://your-domain.com/storage/projects/image1.jpg",
            "http://your-domain.com/storage/projects/image2.jpg"
        ],
        "author": "Adi Warsa",
        "date": "2024-01-01",
        "description": "Project description",
        "technologies": ["React", "Laravel"],
        "features": ["Feature 1", "Feature 2"],
        "status": "Active Development",
        "liveUrl": "https://example.com",
        "githubUrl": "https://github.com/username/repo",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

### 4. Update Project
**PUT** `/projects/{id}`

**Request Body:** (Same as Create Project)

**Response:**
```json
{
    "data": {
        "id": 1,
        "title": "Updated Project",
        "type": "website",
        "images": [
            "http://your-domain.com/storage/projects/image1.jpg",
            "http://your-domain.com/storage/projects/image2.jpg"
        ],
        "author": "Adi Warsa",
        "date": "2024-01-01",
        "description": "Updated description",
        "technologies": ["React", "Laravel", "Vue"],
        "features": ["Feature 1", "Feature 2", "Feature 3"],
        "status": "Completed",
        "liveUrl": "https://example.com",
        "githubUrl": "https://github.com/username/repo",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

### 5. Delete Project
**DELETE** `/projects/{id}`

**Response:**
```json
{
    "message": "Project deleted successfully"
}
```

## Key Features

### Portfolio-Optimized
- **No Pagination**: All projects returned in a single request for portfolio display
- **Latest First**: Projects ordered by creation date (newest first)
- **Filtering**: Filter by type, status, or technology
- **Portfolio Analytics**: Built-in statistics for portfolio overview

### Multiple Images Support
- Projects now support multiple images stored as an array
- Images are returned as full URLs for easy access
- Empty images array is returned as `[]` instead of `null`

### Consistent Response Format
- All responses use Laravel API Resources for consistent formatting
- Images are automatically converted to full URLs
- Arrays (technologies, features, images) are properly handled

### Error Handling
- Proper HTTP status codes (201 for creation, 200 for updates, etc.)
- Validation errors are returned in standard Laravel format
- 404 errors for non-existent projects

## Notes
- The `gradient` field has been removed from the API
- Images are stored as relative paths in the database but returned as full URLs
- All timestamps are in ISO 8601 format
- The API maintains backward compatibility with existing clients 