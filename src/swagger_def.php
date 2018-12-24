<?php
/**
 * PHP version 7.2
 * src\swagger_def.php
 */

use Swagger\Annotations as SWG;

/**
 * Global api definition
 *
 * @SWG\Swagger(
 *   schemes       = { "http", "https" },
 *   host          = "localhost:8000",
 *   consumes      = { "application/json" },
 *   produces      = { "application/json" },
 *   basePath      = "/api/v1",
 *   tags          = {
 *              {
 *                  "name": "login",
 *                  "description": "user login"
 *              },
 *              {
 *                  "name": "Users",
 *                  "description": "User management"
 *              }
 *   },
 *   @SWG\Info(
 *     title       = "TDW18 User REST api",
 *     version     = "0.1.0",
 *     description = "[UPM] TDW18 User REST api operations",
 *     license     = {
 *              "name": "MIT",
 *              "url": "/api-docs/LICENSE.txt"
 *          }
 *   )
 * )
 */

/**
 * Security schema definition
 *
 * @SWG\SecurityScheme(
 *     securityDefinition   = "ResultsSecurity",
 *     type                 = "apiKey",
 *     in                   = "header",
 *     name                 = "X-Token"
 * )
 */

/**
 * Message definition
 *
 * @SWG\Definition(
 *     definition="Message",
 *     required = { "code", "message" },
 *     example = {
 *          "code"    = "HTTP code",
 *          "message" = "Response Message"
 *     },
 *     @SWG\Property(
 *          property    = "code",
 *          description = "Response code",
 *          type        = "integer",
 *          format      = "int32"
 *     ),
 *     @SWG\Property(
 *          property    = "message",
 *          description = "Response message",
 *          type        = "string"
 *      )
 * )
 */

/**
 * Standard Responses definitions
 *
 * @SWG\Response(
 *     response         = "401_Standard_Response",
 *     description      = "`Unauthorized` invalid `X-Token` header",
 *     schema           = {
 *          "$ref": "#/definitions/Message"
 *     }
 * )
 *
 * @SWG\Response(
 *     response         = "403_Forbidden_Response",
 *     description      = "`Forbidden` You don't have permission to access",
 *     schema           = {
 *          "$ref": "#/definitions/Message"
 *     }
 * )
 *
 * @SWG\Response(
 *     response         = "404_Resource_Not_Found_Response",
 *     description      = "`Not found` resource not found",
 *     schema           = {
 *          "$ref": "#/definitions/Message"
 *     }
 * )
 */
