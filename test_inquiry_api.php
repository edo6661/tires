<?php

/**
 * Test script for Inquiry API
 * This demonstrates how to use the inquiry endpoints
 */

// Test data for inquiry submission
$inquiryData = [
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'phone' => '081234567890',
    'subject' => 'Question about tire storage service',
    'message' => 'Hello, I would like to know more about your tire storage service. What are the monthly rates and what security measures do you have in place?'
];

// Test data for contact submission
$contactData = [
    'name' => 'Jane Smith', 
    'email' => 'jane.smith@example.com',
    'subject' => 'General inquiry about services',
    'message' => 'Hi, I need more information about your tire services and pricing.'
];

echo "=== INQUIRY API TEST DATA ===\n\n";

echo "1. INQUIRY SUBMISSION:\n";
echo "Endpoint: POST /api/v1/public/inquiry\n";
echo "Data: " . json_encode($inquiryData, JSON_PRETTY_PRINT) . "\n\n";

echo "Expected Response:\n";
echo json_encode([
    'success' => true,
    'data' => [
        'inquiry_id' => 'generated_id',
        'reference_number' => 'YmdHis + id'
    ],
    'message' => 'Thank you for your inquiry! We will get back to you soon.'
], JSON_PRETTY_PRINT) . "\n\n";

echo "2. CONTACT SUBMISSION:\n";
echo "Endpoint: POST /api/v1/public/contact\n";
echo "Data: " . json_encode($contactData, JSON_PRETTY_PRINT) . "\n\n";

echo "Expected Response:\n";
echo json_encode([
    'success' => true,
    'data' => [
        'contact_id' => 'generated_id',
        'reference_number' => 'YmdHis + id'
    ],
    'message' => 'Thank you for your message! We will get back to you soon.'
], JSON_PRETTY_PRINT) . "\n\n";

echo "=== VALIDATION RULES ===\n";
echo "Inquiry fields:\n";
echo "- name: required|string|max:255\n";
echo "- email: required|email|max:255\n";
echo "- phone: nullable|string|max:20\n";
echo "- subject: required|string|max:255\n";
echo "- message: required|string|max:2000\n\n";

echo "Contact fields:\n";
echo "- name: required|string|max:255\n";
echo "- email: required|email|max:255\n";
echo "- subject: required|string|max:255\n";
echo "- message: required|string|max:2000\n";
echo "- phone: NOT included in contact form\n\n";

echo "=== FEATURES ===\n";
echo "✅ Public endpoints (no authentication required)\n";
echo "✅ Auto-detect logged-in users (user_id assignment)\n";
echo "✅ Input validation and sanitization\n";
echo "✅ Event system (InquirySubmitted event)\n";
echo "✅ Reference number generation\n";
echo "✅ Standardized API responses\n";
echo "✅ Error handling with detailed messages\n\n";

echo "=== USAGE EXAMPLES ===\n";
echo "JavaScript/Frontend:\n";
echo "
const submitInquiry = async (data) => {
    const response = await fetch('/api/v1/public/inquiry', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    });
    return await response.json();
};

// Usage
const result = await submitInquiry({
    name: 'Customer Name',
    email: 'customer@email.com',
    phone: '081234567890',
    subject: 'Question about services',
    message: 'Your inquiry message here...'
});
";
echo "\n";

echo "cURL example:\n";
echo "
curl -X POST http://your-domain.com/api/v1/public/inquiry \\
  -H 'Content-Type: application/json' \\
  -H 'Accept: application/json' \\
  -d '{
    \"name\": \"John Doe\",
    \"email\": \"john@example.com\",
    \"phone\": \"081234567890\",
    \"subject\": \"Question about tire service\",
    \"message\": \"I need more information about your services\"
  }'
";

echo "\n=== IMPLEMENTATION COMPLETE ===\n";
echo "The inquiry API is ready to use! 🚀\n";