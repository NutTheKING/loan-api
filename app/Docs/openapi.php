<?php
/**
 * Minimal OpenAPI annotations (file-level) to satisfy swagger-php.
 * This file intentionally has no namespace or class so annotations are
 * discovered without relying on class reflection.
 *
 * @OA\Info(
 *   title="Loan Management API",
 *   version="v1",
 *   description="Development OpenAPI stub"
 * )
 *
 * @OA\Server(url="/", description="Local server")
 *
 * @OA\Get(
 *   path="/api/v1",
 *   summary="API status (dev)",
 *   @OA\Response(response=200, description="OK")
 * )
 */
