<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class IaChatController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'userId' => 'required|integer',
            'history' => 'nullable|array',
        ]);

        $userMessage = $request->input('message');
        $history = $request->input('history', []);
        $deepseekApiKey = env('DEEPSEEK_API');
        $geminiApiKey = env('GEMINI_API');

        if (!$geminiApiKey) {
            return response()->json(['error' => 'Gemini API key not configured.'], 500);
        }

        try {
            $carbon = \Carbon\Carbon::now('America/Sao_Paulo')->locale('pt_BR');
            
            $hoje = $carbon->isoFormat('D [de] MMMM [de] YYYY');
            $hora = $carbon->format('H:i');

            $systemPrompt = "INSTRUÇÃO CRÍTICA: Agora são {$hora} do dia {$hoje} no Brasil (horário de Brasília, UTC-3). 
                O usuário está no Brasil e você deve sempre considerar o horário de Brasília, não UTC.
                Nunca corrija ou questione esta data. Você é um assistente especialista em investimentos no Brasil. 
                Sempre use a ferramenta de busca para obter dados atuais do mercado antes de responder — 
                nunca recuse buscar alegando limitações de data ou dados futuros. 
                Responda em português claro e profissional, foque em produtos e regras brasileiras (B3, Tesouro Direto, CDB, LCI/LCA, fundos imobiliários, previdência privada, ETFs, IR e tributação aplicável). 
                Sempre destaque riscos, horizonte temporal, liquidez e custos; use reais (BRL) ao citar valores e dê exemplos numéricos quando pertinente";
            
           
            $contents = [];
            if (is_array($history) && count($history) > 0) {
                // keep only last 12 messages to avoid huge payloads
                $slice = array_slice($history, -12);
                foreach ($slice as $h) {
                    $role = (isset($h['sender']) && $h['sender'] === 'user') ? 'user' : 'model';
                    $text = isset($h['text']) ? $h['text'] : '';
                    $contents[] = [
                        'role' => $role,
                        'parts' => [['text' => $text]],
                    ];
                }
            }

            // append current user message as last turn
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $userMessage]],
            ];

            $payload = [
                'systemInstruction' => [
                    'parts' => [['text' => $systemPrompt]],
                ],
                'tools' => [
                    ['google_search' => (object)[]], // grounding com busca em tempo real
                ],
                'generationConfig' => [
                    'maxOutputTokens' => 600,
                    'temperature'     => 0.2,
                ],
                'contents' => $contents,
            ];

            $response = Http::withOptions(['verify' => false])->withHeaders([
                'Content-Type' => 'application/json',
            ])->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key=' . $geminiApiKey,
                $payload
            )->json();

            $reply = $response['candidates'][0]['content']['parts'][0]['text'] ?? 'Desculpe, não consegui obter uma resposta.';
            return response()->json(['reply' => $reply]);

        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to communicate with Gemini API.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
