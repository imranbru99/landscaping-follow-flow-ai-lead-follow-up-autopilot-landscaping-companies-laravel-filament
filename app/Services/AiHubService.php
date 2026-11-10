<?php

namespace App\Services;

use App\Models\Lead;

/**
 * Local equivalent of imrandevbd/laravel-ai-hub — draft/classify/extract for landscaping lead follow-ups.
 */
class AiHubService
{
    public function draftFollowUp(Lead $lead, string $channel = 'email'): array
    {
        $interest = $lead->service_interest ?: 'landscaping service';
        $name = $lead->name;

        $subject = match ($channel) {
            'sms' => null,
            default => "Quick follow-up on your {$interest} request — LandscapingFollowFlow",
        };

        $body = match ($channel) {
            'sms' => "Hi {$name}, this is your landscaping company following up on your {$interest} inquiry. Reply YES to book an estimate, or STOP to opt out.",
            default => "Hi {$name},\n\nThanks for reaching out about {$interest}. Our landscaping crews can help with lawn care, hardscaping, irrigation, tree trimming, and seasonal cleanups.\n\nReply to this email or call us to schedule a convenient estimate window. If you prefer not to hear from us, reply STOP and we will remove you immediately.\n\n— Your LandscapingFollowFlow team",
        };

        $classification = $this->classifyLead($lead);

        return [
            'subject' => $subject,
            'body' => $body,
            'classification' => $classification['label'],
            'confidence' => $classification['confidence'],
        ];
    }

    public function classifyLead(Lead $lead): array
    {
        $text = strtolower(($lead->service_interest ?? '').' '.($lead->notes ?? '').' '.($lead->source ?? ''));

        if (str_contains($text, 'emergency') || str_contains($text, 'storm') || str_contains($text, 'fallen tree')) {
            return ['label' => 'urgent_qualified', 'confidence' => 96.5];
        }

        if (str_contains($text, 'hardscape') || str_contains($text, 'irrigation') || str_contains($text, 'sod') || str_contains($text, 'patio')) {
            return ['label' => 'high_value', 'confidence' => 91.0];
        }

        if (str_contains($text, 'quote') || str_contains($text, 'estimate')) {
            return ['label' => 'estimate_ready', 'confidence' => 88.0];
        }

        return ['label' => 'nurture', 'confidence' => 74.5];
    }

    public function classifyReply(string $reply): array
    {
        $text = strtolower($reply);

        if (preg_match('/\b(yes|interested|book|schedule|tomorrow|available)\b/', $text)) {
            return ['label' => 'qualified_hot', 'confidence' => 94.0, 'escalate' => true];
        }

        if (preg_match('/\b(stop|unsubscribe|remove|no)\b/', $text)) {
            return ['label' => 'opt_out', 'confidence' => 99.0, 'escalate' => false];
        }

        return ['label' => 'neutral', 'confidence' => 70.0, 'escalate' => false];
    }

    public function extractServiceSignals(string $text): array
    {
        $signals = [];
        foreach (['lawn care', 'hardscaping', 'irrigation', 'mulch', 'tree trimming', 'sod install', 'patio', 'seasonal cleanup'] as $term) {
            if (stripos($text, $term) !== false) {
                $signals[] = $term;
            }
        }

        return $signals;
    }
}
