<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class WebhookController extends Controller
{
    private $client;
    private $fonnteToken;
    private $authorizedNumbers;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'connect_timeout' => 10,
        ]);

        // Get token from environment variable
        $this->fonnteToken = 'GWNoRy#ngyYRD@4!zZnX';

        // Authorized phone numbers that can use the webhook
        $this->authorizedNumbers = [
            '6289621791541',
        ];
    }

    /**
     * Check if the sender number is authorized
     */
    private function isAuthorizedNumber(?string $sender): bool
    {
        if (!$sender) {
            return false;
        }

        // Clean the phone number (remove +, spaces, etc.)
        $cleanNumber = preg_replace('/[^0-9]/', '', $sender);

        return in_array($cleanNumber, $this->authorizedNumbers);
    }

    /**
     * Handle incoming webhook from Fonnte (supports both GET and POST methods)
     */
    public function handleFonnteWebhook(Request $request): JsonResponse
    {
        try {
            $method = $request->method();

            // Get data based on request method
            if ($method === 'GET') {
                $data = $request->query();
            } else {
                // POST method - get data from request body
                $data = $request->all();

                // If no data in request body, try to get from query parameters
                if (empty($data)) {
                    $data = $request->query();
                }
            }

            // Extract webhook data
            $device = $data['device'] ?? null;
            $sender = $data['sender'] ?? null;
            $message = $data['message'] ?? null;
            $text = $data['text'] ?? null; // button text
            $member = $data['member'] ?? null; // group member who send the message
            $name = $data['name'] ?? null;
            $location = $data['location'] ?? null;
            $pollname = $data['pollname'] ?? null;
            $choices = $data['choices'] ?? null;

            // Data below will only be received by device with all feature package
            $url = $data['url'] ?? null;
            $filename = $data['filename'] ?? null;
            $extension = $data['extension'] ?? null;

            // Log incoming webhook for debugging
            Log::info('Fonnte webhook received', [
                'method' => $method,
                'device' => $device,
                'sender' => $sender,
                'message' => $message,
                'name' => $name,
                'content_type' => $request->header('Content-Type'),
                'has_data' => !empty($data),
                'data_source' => $method === 'GET' ? 'query_params' : 'request_body'
            ]);

            // Check if sender is authorized - if not, return early without processing
            if (!$this->isAuthorizedNumber($sender)) {
                Log::warning('Unauthorized webhook access attempt - no message sent', [
                    'sender' => $sender,
                    'method' => $method,
                    'message' => $message
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. Your phone number is not authorized to use this webhook.',
                    'sender' => $sender,
                    'authorized' => false,
                    'message_sent' => false
                ], 403);
            }

            // Only process message if sender is authorized
            $reply = $this->processMessage($message);

            // Send reply only to authorized numbers
            $response = null;
            if ($sender) {
                $response = $this->sendFonnte($sender, $reply);
            } else {
                Log::warning('No sender provided in webhook request', [
                    'method' => $method,
                    'data' => $data
                ]);
                $response = [
                    'success' => false,
                    'error' => 'No sender provided'
                ];
            }

            return response()->json([
                'success' => true,
                'message' => "Webhook processed successfully ({$method})",
                'response' => $response,
                'sender' => $sender,
                'processed_message' => $reply['message'] ?? null,
                'authorized' => true,
                'message_sent' => true
            ]);
        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'method' => $request->method(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing webhook: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process incoming message and generate appropriate reply
     */
    private function processMessage(?string $message): array
    {
        if (!$message) {
            return [
                'message' => 'Sorry, i don\'t understand. Please use one of the following keyword :' . "\n\n" .
                    'Test' . "\n" .
                    'Audio' . "\n" .
                    'Video' . "\n" .
                    'Image' . "\n" .
                    'File' . "\n" .
                    '!approve {booking_number}' . "\n" .
                    '!reject {booking_number}'
            ];
        }

        $message = trim($message);

        // Check for booking approval command
        if (preg_match('/^!approve\s+(.+)$/i', $message, $matches)) {
            $bookingNumber = trim($matches[1]);
            return $this->processBookingApproval($bookingNumber);
        }

        // Check for booking rejection command
        if (preg_match('/^!reject\s+(.+)$/i', $message, $matches)) {
            $bookingNumber = trim($matches[1]);
            return $this->processBookingRejection($bookingNumber);
        }

        $message = strtolower($message);

        switch ($message) {
            case 'test':
                return [
                    'message' => 'Masuk!'
                ];

            case 'image':
                return [
                    'message' => 'Image message',
                    'url' => 'https://filesamples.com/samples/image/jpg/sample_640%C3%97426.jpg'
                ];

            case 'audio':
                return [
                    'message' => 'Audio message',
                    'url' => 'https://filesamples.com/samples/audio/mp3/sample3.mp3',
                    'filename' => 'music'
                ];

            case 'video':
                return [
                    'message' => 'Video message',
                    'url' => 'https://filesamples.com/samples/video/mp4/sample_640x360.mp4'
                ];

            case 'file':
                return [
                    'message' => 'File message',
                    'url' => 'https://filesamples.com/samples/document/docx/sample3.docx',
                    'filename' => 'document'
                ];

            default:
                return [
                    'message' => 'Sorry, i don\'t understand. Please use one of the following keyword :' . "\n\n" .
                        'Test' . "\n" .
                        'Audio' . "\n" .
                        'Video' . "\n" .
                        'Image' . "\n" .
                        'File' . "\n" .
                        '!approve {booking_number}' . "\n" .
                        '!reject {booking_number}'
                ];
        }
    }

    /**
     * Process booking approval command
     */
    private function processBookingApproval(string $bookingNumber): array
    {
        try {
            // Mock booking data instead of database query
            $booking = $this->getMockBooking($bookingNumber);

            if (!$booking) {
                return [
                    'message' => "❌ Booking not found!\n\nBooking number: {$bookingNumber}\n\nPlease check the booking number and try again."
                ];
            }

            // Check if booking is in draft status
            if ($booking['status'] !== 'D') {
                $statusText = $this->getStatusText($booking['status']);
                return [
                    'message' => "❌ Booking cannot be approved!\n\nBooking number: {$bookingNumber}\nCurrent status: {$statusText}\n\nOnly draft bookings can be approved."
                ];
            }

            // Simulate updating booking status from Draft (D) to None (N)
            $booking['status'] = 'N';
            $booking['updated_at'] = Carbon::now();

            // Mock booking details
            $bookingDetails = $this->getMockBookingDetails($bookingNumber);

            // Mock member info
            $member = $this->getMockMember($booking['member_code']);

            // Mock agent info
            $agent = $this->getMockAgent($booking['agent_code']);

            // Build response message
            $responseMessage = "✅ Booking approved successfully!\n\n";
            $responseMessage .= "📋 *Booking Details:*\n";
            $responseMessage .= "• Booking No: {$bookingNumber}\n";
            $responseMessage .= "• Date: " . date('d M Y', strtotime($booking['date'])) . "\n";
            $responseMessage .= "• Type: " . ($booking['type'] === 'W' ? 'Walk In' : 'Online') . "\n";
            $responseMessage .= "• Status: None (Approved)\n";
            $responseMessage .= "• Total Amount: Rp " . number_format($booking['nett'], 0, ',', '.') . "\n";

            if ($member) {
                $responseMessage .= "• Customer: {$member['name']}\n";
                $responseMessage .= "• Phone: {$member['phone']}\n";
            }

            if ($agent) {
                $responseMessage .= "• Agent: {$agent['name']}\n";
            }

            if (count($bookingDetails) > 0) {
                $responseMessage .= "\n📦 *Packages:*\n";
                foreach ($bookingDetails as $detail) {
                    $responseMessage .= "• {$detail['name']} (Qty: {$detail['qty']})\n";
                }
            }

            $responseMessage .= "\n🎉 The booking has been approved and is now active!";

            Log::info('Booking approved via webhook', [
                'booking_number' => $bookingNumber,
                'old_status' => 'D',
                'new_status' => 'N',
                'approved_at' => Carbon::now()
            ]);

            return [
                'message' => $responseMessage
            ];
        } catch (\Exception $e) {
            Log::error('Booking approval error', [
                'booking_number' => $bookingNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'message' => "❌ Error approving booking!\n\nBooking number: {$bookingNumber}\nError: " . $e->getMessage() . "\n\nPlease try again or contact support."
            ];
        }
    }

    /**
     * Process booking rejection command
     */
    private function processBookingRejection(string $bookingNumber): array
    {
        try {
            // Mock booking data instead of database query
            $booking = $this->getMockBooking($bookingNumber);

            if (!$booking) {
                return [
                    'message' => "❌ Booking not found!\n\nBooking number: {$bookingNumber}\n\nPlease check the booking number and try again."
                ];
            }

            // Check if booking is in draft status
            if ($booking['status'] !== 'D') {
                $statusText = $this->getStatusText($booking['status']);
                return [
                    'message' => "❌ Booking cannot be rejected!\n\nBooking number: {$bookingNumber}\nCurrent status: {$statusText}\n\nOnly draft bookings can be rejected."
                ];
            }

            // Simulate updating booking status from Draft (D) to Cancelled (C)
            $booking['status'] = 'C';
            $booking['updated_at'] = Carbon::now();

            // Mock booking details
            $bookingDetails = $this->getMockBookingDetails($bookingNumber);

            // Mock member info
            $member = $this->getMockMember($booking['member_code']);

            // Mock agent info
            $agent = $this->getMockAgent($booking['agent_code']);

            // Build response message
            $responseMessage = "✅ Booking rejected successfully!\n\n";
            $responseMessage .= "📋 *Booking Details:*\n";
            $responseMessage .= "• Booking No: {$bookingNumber}\n";
            $responseMessage .= "• Date: " . date('d M Y', strtotime($booking['date'])) . "\n";
            $responseMessage .= "• Type: " . ($booking['type'] === 'W' ? 'Walk In' : 'Online') . "\n";
            $responseMessage .= "• Status: Cancelled\n";
            $responseMessage .= "• Total Amount: Rp " . number_format($booking['nett'], 0, ',', '.') . "\n";

            if ($member) {
                $responseMessage .= "• Customer: {$member['name']}\n";
                $responseMessage .= "• Phone: {$member['phone']}\n";
            }

            if ($agent) {
                $responseMessage .= "• Agent: {$agent['name']}\n";
            }

            if (count($bookingDetails) > 0) {
                $responseMessage .= "\n📦 *Packages:*\n";
                foreach ($bookingDetails as $detail) {
                    $responseMessage .= "• {$detail['name']} (Qty: {$detail['qty']})\n";
                }
            }

            $responseMessage .= "\n🎉 The booking has been rejected and is now cancelled!";

            Log::info('Booking rejected via webhook', [
                'booking_number' => $bookingNumber,
                'old_status' => 'D',
                'new_status' => 'C',
                'rejected_at' => Carbon::now()
            ]);

            return [
                'message' => $responseMessage
            ];
        } catch (\Exception $e) {
            Log::error('Booking rejection error', [
                'booking_number' => $bookingNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'message' => "❌ Error rejecting booking!\n\nBooking number: {$bookingNumber}\nError: " . $e->getMessage() . "\n\nPlease try again or contact support."
            ];
        }
    }

    /**
     * Get mock booking data
     */
    private function getMockBooking(string $bookingNumber): ?array
    {
        // Mock booking data - you can customize this
        $mockBookings = [
            'BK001' => [
                'no' => 'BK001',
                'date' => '2025-07-15',
                'type' => 'W', // W = Walk In, O = Online
                'status' => 'D', // D = Draft, N = None, S = Show Up, C = Cancelled
                'nett' => 1500000,
                'member_code' => 'M001',
                'agent_code' => 'A001',
                'updated_at' => Carbon::now()
            ],
            'BK002' => [
                'no' => 'BK002',
                'date' => '2025-07-16',
                'type' => 'O',
                'status' => 'D',
                'nett' => 2500000,
                'member_code' => 'M002',
                'agent_code' => 'A002',
                'updated_at' => Carbon::now()
            ],
            'BK003' => [
                'no' => 'BK003',
                'date' => '2025-07-17',
                'type' => 'W',
                'status' => 'N', // Already approved
                'nett' => 800000,
                'member_code' => 'M003',
                'agent_code' => 'A001',
                'updated_at' => Carbon::now()
            ]
        ];

        return $mockBookings[$bookingNumber] ?? null;
    }

    /**
     * Get mock booking details
     */
    private function getMockBookingDetails(string $bookingNumber): array
    {
        // Mock booking details data
        $mockDetails = [
            'BK001' => [
                [
                    'no' => 'BK001',
                    'name' => 'Wedding Package Premium',
                    'qty' => 1
                ],
                [
                    'no' => 'BK001',
                    'name' => 'Photography Service',
                    'qty' => 1
                ]
            ],
            'BK002' => [
                [
                    'no' => 'BK002',
                    'name' => 'Birthday Party Package',
                    'qty' => 1
                ],
                [
                    'no' => 'BK002',
                    'name' => 'Catering Service',
                    'qty' => 1
                ],
                [
                    'no' => 'BK002',
                    'name' => 'Decoration Service',
                    'qty' => 1
                ]
            ],
            'BK003' => [
                [
                    'no' => 'BK003',
                    'name' => 'Corporate Event Package',
                    'qty' => 1
                ]
            ]
        ];

        return $mockDetails[$bookingNumber] ?? [];
    }

    /**
     * Get mock member data
     */
    private function getMockMember(?string $memberCode): ?array
    {
        if (!$memberCode) {
            return null;
        }

        // Mock member data
        $mockMembers = [
            'M001' => [
                'code' => 'M001',
                'name' => 'John Doe',
                'phone' => '+6281234567890'
            ],
            'M002' => [
                'code' => 'M002',
                'name' => 'Jane Smith',
                'phone' => '+6289876543210'
            ],
            'M003' => [
                'code' => 'M003',
                'name' => 'Bob Johnson',
                'phone' => '+6281122334455'
            ]
        ];

        return $mockMembers[$memberCode] ?? null;
    }

    /**
     * Get mock agent data
     */
    private function getMockAgent(?string $agentCode): ?array
    {
        if (!$agentCode) {
            return null;
        }

        // Mock agent data
        $mockAgents = [
            'A001' => [
                'code' => 'A001',
                'name' => 'Sarah Wilson'
            ],
            'A002' => [
                'code' => 'A002',
                'name' => 'Mike Brown'
            ]
        ];

        return $mockAgents[$agentCode] ?? null;
    }

    /**
     * Get human-readable status text
     */
    private function getStatusText(string $status): string
    {
        return match ($status) {
            'D' => 'Draft',
            'N' => 'None',
            'S' => 'Show Up',
            'C' => 'Cancelled',
            default => 'Unknown'
        };
    }

    /**
     * Send message to Fonnte using Guzzle HTTP
     */
    private function sendFonnte(string $target, array $data): array
    {
        try {
            if (empty($this->fonnteToken)) {
                throw new \Exception('Fonnte token not configured');
            }

            $postData = [
                'target' => $target,
                'message' => $data['message']
            ];

            // Add optional fields if they exist
            if (isset($data['url'])) {
                $postData['url'] = $data['url'];
            }

            if (isset($data['filename'])) {
                $postData['filename'] = $data['filename'];
            }

            $response = $this->client->post('https://api.fonnte.com/send', [
                'headers' => [
                    'Authorization' => $this->fonnteToken,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'User-Agent' => 'Laravel-POS-Odyssey/1.0'
                ],
                'form_params' => $postData
            ]);

            $responseBody = $response->getBody()->getContents();
            $responseData = json_decode($responseBody, true);

            Log::info('Fonnte API response', [
                'status_code' => $response->getStatusCode(),
                'response' => $responseData
            ]);

            return [
                'success' => $response->getStatusCode() === 200,
                'status_code' => $response->getStatusCode(),
                'response' => $responseData
            ];
        } catch (GuzzleException $e) {
            Log::error('Fonnte API error', [
                'error' => $e->getMessage(),
                'target' => $target,
                'data' => $data
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error('Fonnte send error', [
                'error' => $e->getMessage(),
                'target' => $target,
                'data' => $data
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Test endpoint to verify webhook is working
     */
    public function test(Request $request): JsonResponse
    {
        $method = $request->method();

        return response()->json([
            'success' => true,
            'message' => 'Webhook endpoint is working!',
            'method' => $method,
            'timestamp' => Carbon::now()->toISOString(),
            'endpoints' => [
                'test' => 'GET/POST /api/webhook/test',
                'fonnte_webhook' => 'GET/POST /api/webhook/fonnte',
                'test_message' => 'GET/POST /api/webhook/test-message',
                'check_auth' => 'GET/POST /api/webhook/check-auth?number=6281234567890'
            ],
            'supported_commands' => [
                'test',
                'image',
                'audio',
                'video',
                'file',
                '!approve {booking_number}',
                '!reject {booking_number}'
            ],
            'authorized_numbers' => $this->authorizedNumbers,
            'example_requests' => [
                'get' => '/api/webhook/fonnte?sender=6281234567890&message=test',
                'post' => 'POST /api/webhook/fonnte with JSON body: {"sender": "6281234567890", "message": "test"}'
            ]
        ]);
    }

    /**
     * Check if a phone number is authorized
     */
    public function checkAuth(Request $request): JsonResponse
    {
        $method = $request->method();

        // Get number from appropriate source based on method
        if ($method === 'GET') {
            $number = $request->query('number');
        } else {
            // POST method - try request body first, then query params
            $data = $request->all();
            $number = $data['number'] ?? $request->query('number');
        }

        if (!$number) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a number parameter',
                'method' => $method,
                'examples' => [
                    'get' => '/api/webhook/check-auth?number=6281234567890',
                    'post' => 'POST /api/webhook/check-auth with JSON body: {"number": "6281234567890"}'
                ]
            ], 400);
        }

        $isAuthorized = $this->isAuthorizedNumber($number);

        return response()->json([
            'success' => true,
            'method' => $method,
            'number' => $number,
            'authorized' => $isAuthorized,
            'authorized_numbers' => $this->authorizedNumbers,
            'timestamp' => Carbon::now()->toISOString()
        ]);
    }

    /**
     * Test message processing without sending (supports both GET and POST)
     */
    public function testMessage(Request $request): JsonResponse
    {
        $method = $request->method();

        // Get message from appropriate source based on method
        if ($method === 'GET') {
            $message = $request->query('message');
        } else {
            // POST method - try request body first, then query params
            $data = $request->all();
            $message = $data['message'] ?? $request->query('message');
        }

        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a message parameter',
                'method' => $method,
                'examples' => [
                    'get' => '/api/webhook/test-message?message=test',
                    'post' => 'POST /api/webhook/test-message with JSON body: {"message": "test"}'
                ]
            ], 400);
        }

        $reply = $this->processMessage($message);

        return response()->json([
            'success' => true,
            'method' => $method,
            'input_message' => $message,
            'processed_reply' => $reply,
            'timestamp' => Carbon::now()->toISOString()
        ]);
    }
}
