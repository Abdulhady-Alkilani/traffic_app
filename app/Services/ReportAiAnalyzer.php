<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Report;
use Illuminate\Support\Facades\Log;

class ReportAiAnalyzer
{
    public function __construct(
        private readonly AiService $aiService,
    ) {}

    /**
     * Run full AI analysis on a report.
     */
    public function analyze(Report $report): void
    {
        try {
            // Build the multimodal content parts (image/video)
            $contentParts = $this->buildContentParts($report);

            // Proceed if there is media OR a text description to analyze
            if (empty($contentParts) && empty($report->description)) {
                Log::info('AI Analyzer: No content to analyze for report #' . $report->id);
                return;
            }

            // Run comprehensive analysis
            $analysisResult = $this->runComprehensiveAnalysis($report, $contentParts);

            if ($analysisResult) {
                $isAiGenerated = isset($analysisResult['is_ai_generated']) && $analysisResult['is_ai_generated'] === true;

                if ($isAiGenerated) {
                    $analysisResult['summary'] = '❌ تم الرفض: اكتشف النظام أن المرفقات مولدة بالذكاء الاصطناعي (مفبركة) ولا تمثل حادثة حقيقية. ' . ($analysisResult['summary'] ?? '');
                }

                $updateData = [
                    'ai_detected_plate' => $analysisResult['detected_plate'] ?? null,
                    'ai_incident_type' => $analysisResult['incident_type'] ?? null,
                    'ai_severity_score' => isset($analysisResult['severity_score'])
                        ? max(1, min(5, (int) $analysisResult['severity_score']))
                        : null,
                    'ai_damage_assessment' => $analysisResult['damage_assessment'] ?? null,
                    'ai_summary' => $analysisResult['summary'] ?? null,
                    'ai_analyzed_at' => now(),
                ];

                if ($isAiGenerated) {
                    $updateData['status'] = \App\Enums\ReportStatus::Rejected;
                }

                // Check for duplicates
                $duplicateResult = $this->checkDuplicate($report);
                if ($duplicateResult) {
                    $updateData['ai_is_duplicate'] = $duplicateResult['is_duplicate'];
                    $updateData['ai_duplicate_of'] = $duplicateResult['duplicate_of_id'];
                }

                $report->update($updateData);

                Log::info('AI Analyzer: Successfully analyzed report #' . $report->id, [
                    'severity' => $updateData['ai_severity_score'],
                    'incident_type' => $updateData['ai_incident_type'],
                    'detected_plate' => $updateData['ai_detected_plate'],
                    'is_duplicate' => $updateData['ai_is_duplicate'] ?? false,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('AI Analyzer: Failed to analyze report #' . $report->id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Build multimodal content parts from the report's media.
     */
    private function buildContentParts(Report $report): array
    {
        $parts = [];

        // Add image if exists
        if ($report->image_url) {
            $imagePart = $this->aiService->buildImageContent($report->image_url);
            if ($imagePart) {
                $parts[] = $imagePart;
            }
        }

        // Add video if exists
        if ($report->video_url) {
            $videoPart = $this->aiService->buildVideoContent($report->video_url);
            if ($videoPart) {
                $parts[] = $videoPart;
            }
        }

        return $parts;
    }

    /**
     * Run comprehensive AI analysis in a single call.
     */
    private function runComprehensiveAnalysis(Report $report, array $mediaParts): ?array
    {
        $prompt = $this->buildAnalysisPrompt($report);

        $content = [];
        $content[] = ['type' => 'text', 'text' => $prompt];

        // Add media parts
        foreach ($mediaParts as $part) {
            $content[] = $part;
        }

        $messages = [
            [
                'role' => 'system',
                'content' => 'أنت محلل بلاغات مرورية متخصص. تقوم بتحليل البلاغات المرورية وتقديم تقييم شامل. أجب دائماً بتنسيق JSON فقط بدون أي نص إضافي أو markdown.',
            ],
            [
                'role' => 'user',
                'content' => $content,
            ],
        ];

        $response = $this->aiService->chat($messages, 0.2);

        if (!$response) {
            return null;
        }

        return $this->parseJsonResponse($response);
    }

    /**
     * Build the analysis prompt.
     */
    private function buildAnalysisPrompt(Report $report): string
    {
        $reportTypeMap = [
            'accident' => 'حادث',
            'hazard' => 'مخالفة مرورية',
            'traffic_jam' => 'ازدحام مروري',
            'security_threat' => 'تهديد أمني',
        ];

        $reportTypeName = $reportTypeMap[$report->report_type] ?? $report->report_type;

        $prompt = <<<PROMPT
حلل البلاغ المروري التالي وأرجع النتائج بتنسيق JSON فقط.

معلومات البلاغ:
- نوع البلاغ المُدخل من المواطن: {$reportTypeName}
- الوصف: {$report->description}
- الموقع: {$report->location_text}
PROMPT;

        if ($report->image_url) {
            $prompt .= "\n- يوجد صورة مرفقة (تم إرسالها معك). حللها بدقة واستخرج منها: رقم اللوحة إن وُجد، نوع الحادث الفعلي، والأضرار المرئية.";
            $prompt .= "\n- تحقق بدقة عالية عما إذا كانت الصورة مولدة بالذكاء الاصطناعي (AI Generated / Deepfake) أو معدلة، وابحث عن التشوهات الشائعة في الصور المولدة.";
        }

        if ($report->video_url) {
            $prompt .= "\n- يوجد فيديو مرفق (تم إرساله معك). حلل محتواه لتقييم الأضرار والخطورة.";
            $prompt .= "\n- تحقق عما إذا كان الفيديو مولداً بالذكاء الاصطناعي.";
        }

        $prompt .= <<<PROMPT


أرجع JSON بالتنسيق التالي بدون أي نص إضافي:
{
    "is_ai_generated": true إذا كانت الصورة/الفيديو مولدة بالذكاء الاصطناعي (مفبركة)، وإلا false,
    "detected_plate": "رقم اللوحة المستخرج من الصورة أو null إذا لم يظهر",
    "incident_type": "accident أو hazard أو traffic_jam أو security_threat",
    "severity_score": رقم من 1 إلى 5 حيث 1=طفيف جداً و5=خطير جداً,
    "damage_assessment": "وصف تفصيلي للأضرار المرئية في الصورة/الفيديو. إذا لم توجد مرفقات اكتب تقييم بناءً على الوصف النصي",
    "summary": "ملخص شامل للبلاغ يتضمن: ماذا حدث، أين، مدى الخطورة، والإجراء الموصى به"
}
PROMPT;

        return $prompt;
    }

    /**
     * Check if this report is a duplicate of an existing one.
     */
    private function checkDuplicate(Report $report): ?array
    {
        if (!$report->latitude || !$report->longitude) {
            return null;
        }

        // Search for reports in the last 24 hours within ~500 meters
        $latRange = 0.0045; // ~500m
        $lngRange = 0.0045;

        $potentialDuplicates = Report::where('id', '!=', $report->id)
            ->where('report_type', $report->report_type)
            ->where('created_at', '>=', now()->subHours(24))
            ->whereBetween('latitude', [
                $report->latitude - $latRange,
                $report->latitude + $latRange,
            ])
            ->whereBetween('longitude', [
                $report->longitude - $lngRange,
                $report->longitude + $lngRange,
            ])
            ->orderBy('created_at', 'asc')
            ->first();

        if ($potentialDuplicates) {
            // Use AI to confirm the duplicate
            $isDuplicate = $this->confirmDuplicateWithAi($report, $potentialDuplicates);

            if ($isDuplicate) {
                return [
                    'is_duplicate' => true,
                    'duplicate_of_id' => $potentialDuplicates->id,
                ];
            }
        }

        return [
            'is_duplicate' => false,
            'duplicate_of_id' => null,
        ];
    }

    /**
     * Use AI to confirm if two reports are duplicates.
     */
    private function confirmDuplicateWithAi(Report $newReport, Report $existingReport): bool
    {
        $prompt = <<<PROMPT
هل البلاغان التاليان يصفان نفس الحادثة المرورية؟ أجب بـ JSON فقط: {"is_duplicate": true} أو {"is_duplicate": false}

البلاغ الأول (الأقدم):
- النوع: {$existingReport->report_type}
- الوصف: {$existingReport->description}
- الموقع: {$existingReport->location_text}
- التاريخ: {$existingReport->created_at}

البلاغ الثاني (الأحدث):
- النوع: {$newReport->report_type}
- الوصف: {$newReport->description}
- الموقع: {$newReport->location_text}
- التاريخ: {$newReport->created_at}
PROMPT;

        $messages = [
            ['role' => 'user', 'content' => $prompt],
        ];

        $response = $this->aiService->chat($messages, 0.1);

        if (!$response) {
            return false;
        }

        $parsed = $this->parseJsonResponse($response);

        return $parsed['is_duplicate'] ?? false;
    }

    /**
     * Parse a JSON response from the AI, handling markdown code blocks.
     */
    private function parseJsonResponse(string $response): ?array
    {
        // Clean up: remove markdown code blocks if present
        $cleaned = trim($response);
        $cleaned = preg_replace('/^```(?:json)?\s*/i', '', $cleaned);
        $cleaned = preg_replace('/\s*```$/i', '', $cleaned);
        $cleaned = trim($cleaned);

        $decoded = json_decode($cleaned, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('AI Analyzer: Failed to parse JSON response', [
                'response' => $response,
                'error' => json_last_error_msg(),
            ]);
            return null;
        }

        return $decoded;
    }
}
