<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PostService;
use App\Http\Resources\Post\PostResource;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\BannedWord;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    protected PostService $service;

    public function __construct(PostService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $post = $this->service->index();
        return PostResource::collection($post);
    }


    public function store(StorePostRequest $request)
    {
    $data = $request->all();

    // 🔹 Verifica se existe campo "text" antes de qualquer coisa
    if (!empty($data['text'])) {
        $text = mb_strtolower($data['text']); // ignora maiúsculas/minúsculas
        $bannedWords = BannedWord::pluck('word')->toArray(); // supondo coluna "word"

        foreach ($bannedWords as $word) {
            // compara ignorando case e palavras dentro do texto
            if (preg_match('/\b' . preg_quote(mb_strtolower($word), '/') . '\b/u', $text)) {
                return response()->json([
                    'error' => "O texto contém uma palavra proibida: '{$word}'.",
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
    }


        $processFiles = function ($files, $parentKey = '') use (&$processFiles) {
            $results = [];

            foreach ($files as $key => $value) {
                $fullKey = $parentKey === '' ? $key : $parentKey . '.' . $key;

                if ($value instanceof \Illuminate\Http\UploadedFile) {
                    $originalName = $value->getClientOriginalName();
                    $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '_', $originalName);

                    $value->move(public_path('post'), $filename);

                    $results[$fullKey] = asset('post/' . $filename);
                    continue;
                }

                if (is_array($value)) {
                    $sub = $processFiles($value, $fullKey);
                    $results = array_merge($results, $sub);
                }
            }

            return $results;
        };

        $allFiles = $request->allFiles();
        $filesProcessed = $processFiles($allFiles);

        foreach ($filesProcessed as $dotKey => $assetPath) {
            $keys = explode('.', $dotKey);
            $ref = &$data;
            foreach ($keys as $i => $k) {
                if ($i === count($keys) - 1) {
                    $ref[$k] = $assetPath;
                } else {
                    if (!isset($ref[$k]) || !is_array($ref[$k])) {
                        $ref[$k] = [];
                    }
                    $ref = &$ref[$k];
                }
            }
            unset($ref); // limpa ref
        }

        $clean = function (&$arr) use (&$clean) {
            foreach ($arr as $k => $v) {
                if ($v instanceof \Illuminate\Http\UploadedFile) {
                    unset($arr[$k]);
                    continue;
                }
                if (is_array($v)) $clean($arr[$k]);
            }
        };
        $clean($data);

        $post = $this->service->store($data);

        return new PostResource($post);
    }

    public function update(UpdatePostRequest $request)
    {
        try {
            $post = $this->service->update($request);
            return new PostResource($post);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['message' => 'Erro ao editar post.'], 500);
        }
    }

    public function delete(int $id)
    {
        try {
            $post = $this->service->destroy($id);
            return new PostResource($post);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['message' => 'Erro ao deletar post.'], 500);
        }
    }


    public function show(int $id)
    {
        try {
            $post = $this->service->show($id);
            return new PostResource($post);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['message' => 'Erro ao buscar post.'], 500);
        }
    }

    public function restore(int $id)
    {
        try {
            $post = $this->service->restore($id);
            return new PostResource($post);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['message' => 'Erro ao buscar post.'], 500);
        }
    }
}
