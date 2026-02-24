<?php
/**
 * Minimal OpenAPI annotations to satisfy swagger-php / l5-swagger generator.
 * This file is for development only and provides a basic PathItem and a simple
 * operation so the generator can produce docs without scanning all controllers.
 *
 * @OA\Info(
 *   title="Loan Management API",
 *   version="v1",
 *   description="Auto-generated minimal OpenAPI description for local dev"
 * )
 *
 * @OA\Server(url="/", description="Local server")
 */
namespace App\Http\Controllers\Api\V1\Docs;

/**
 * A dummy class that only carries OpenAPI Path/Operation annotations.
 *
 * @OA\PathItem(path="/api/v1")
 */
class OpenApiDoc
{
    /**
     * Simple status endpoint used by docs generation.
     *
     * @OA\Get(
     *   path="/api/v1",
     *   summary="API status (dev)",
     *   @OA\Response(response=200, description="API is operational")
     * )
     */
    public function status() {}
}
