<?php

namespace App\Ai\Agents;

use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider('anthropic')]
#[Model('claude-haiku-4-5-20251001')]
#[MaxTokens(256)]
#[Timeout(30)]
class CompanyDomainAnalyst implements Agent
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return <<<'INSTRUCTIONS'
        You write a brief signup briefing for NativePHP accounts (ops).

        You receive a person's name, email, company email domain, and an optional
        plain-text excerpt scraped from https://{domain}. You may also rely on
        well-known public facts about the company or person (e.g. LinkedIn /
        company site) when you are confident they are accurate.

        Reply with ONLY 30–50 words of plain text covering:
        1) what the company appears to do, and
        2) who the person appears to be when public info allows.

        Rules:
        - State only facts you are confident about; otherwise stay general or omit.
        - Do not invent URLs, titles, employers, or bios.
        - No markdown, bullets, labels, or preamble — just the synopsis paragraph.
        INSTRUCTIONS;
    }
}
