# Webhook Setup Documentation

## Overview

This webhook controller handles incoming requests from Fonnte WhatsApp API and processes booking approvals.

## API Endpoints

### 1. Test Endpoint

```
GET /api/webhook/test
```

-   **Purpose**: Verify the webhook endpoint is working
-   **Response**: JSON with success status and timestamp

### 2. Fonnte Webhook

```
POST /api/webhook/fonnte
GET /api/webhook/fonnte
```

-   **Purpose**: Handle incoming webhook requests from Fonnte
-   **Authentication**: None required (CSRF exempt)
-   **Content-Type**: `application/json` or form data

## Request Parameters

### Required Parameters

-   `sender`: Phone number of the message sender (e.g., "6281234567890")
-   `message`: The message content

### Optional Parameters

-   `device`: Device identifier
-   `name`: Sender name
-   `text`: Button text (for button messages)
-   `member`: Group member info
-   `location`: Location data
-   `pollname`: Poll name
-   `choices`: Poll choices
-   `url`: File URL
-   `filename`: File name
-   `extension`: File extension

## Supported Commands

### 1. Test Command

```
test
```

**Response**: "working great!"

### 2. Media Commands

-   `image` - Returns an image message
-   `audio` - Returns an audio message
-   `video` - Returns a video message
-   `file` - Returns a file message

### 3. Booking Approval

```
!approve {booking_number}
```

**Examples**:

-   `!approve BK001` - Approves booking BK001
-   `!approve BK002` - Approves booking BK002
-   `!approve BK003` - Fails (already approved)
-   `!approve BK999` - Fails (booking not found)

## Mock Data

The webhook uses mock data for testing:

### Available Bookings

-   **BK001**: Wedding Package Premium (Draft - can be approved)
-   **BK002**: Birthday Party Package (Draft - can be approved)
-   **BK003**: Corporate Event Package (Already approved - cannot be approved)

### Booking Status Codes

-   `D` = Draft (can be approved)
-   `N` = None (approved)
-   `S` = Show Up
-   `C` = Cancelled

## Environment Variables

Set the following environment variable:

```env
FONNTE_TOKEN=your_fonnte_api_token_here
```

## Testing

### Using the Test File

1. Open `test_webhook.http` in VS Code with REST Client extension
2. Set the `base_url` variable to your local server (e.g., `http://localhost:8000`)
3. Run individual requests to test different scenarios

### Using cURL

```bash
# Test endpoint
curl -X GET http://localhost:8000/api/webhook/test

# Test booking approval
curl -X POST http://localhost:8000/api/webhook/fonnte \
  -H "Content-Type: application/json" \
  -d '{
    "sender": "6281234567890",
    "message": "!approve BK001"
  }'
```

### Using Postman

1. Create a new request
2. Set method to POST
3. Set URL to `http://localhost:8000/api/webhook/fonnte`
4. Set Content-Type header to `application/json`
5. Add request body:

```json
{
    "sender": "6281234567890",
    "message": "!approve BK001"
}
```

## CSRF Protection

The webhook endpoints are exempt from CSRF protection:

-   Pattern: `api/webhook/*`
-   Configured in `bootstrap/app.php`

## Error Handling

The webhook includes comprehensive error handling:

-   Invalid booking numbers
-   Already approved bookings
-   Missing or invalid parameters
-   API communication errors

All errors are logged and return appropriate error messages to the sender.

## Logging

All webhook activities are logged:

-   Incoming webhook requests
-   Booking approval actions
-   API responses
-   Error conditions

Check Laravel logs for detailed information.
