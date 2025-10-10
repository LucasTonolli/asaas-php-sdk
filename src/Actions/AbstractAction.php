<?php

namespace AsaasPhpSdk\Actions;

use AsaasPhpSdk\Exceptions\ApiException;
use AsaasPhpSdk\Helpers\ResponseHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

/**
 * Base class for all SDK Actions.
 *
 * This class provides a standardized method to execute API requests,
 * centralizing the error handling logic for HTTP exceptions. All concrete
 * action classes (e.g., CreateCustomerAction) should extend this class.
 *
 * @internal This is an internal class and should not be used directly by SDK consumers.
 */
abstract class AbstractAction
{
    /**
     * AbstractAction constructor.
     *
     * @param  Client  $client  The configured Guzzle HTTP client.
     * @param  ResponseHandler  $responseHandler  The handler responsible for parsing API responses.
     */
    public function __construct(
        protected readonly Client $client,
        protected readonly ResponseHandler $responseHandler
    ) {}

    /**
     * Executes a given request callable with standardized error handling.
     *
     * This method wraps the API call in a try-catch block, handling common
     * Guzzle exceptions. It delegates successful responses and known error
     * responses to the ResponseHandler. Unhandled exceptions are wrapped in
     * a generic ApiException to provide a consistent error contract.
     *
     * @param  callable  $request  A callable function that executes the Guzzle request and returns a ResponseInterface.
     * @return array The associative array parsed from the API response body by the ResponseHandler.
     *
     * @throws ApiException if a connection error or an unhandled client error occurs.
     */
    protected function executeRequest(callable $request): array
    {
        try {
            $response = $request();

            return $this->responseHandler->handle($response);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                return $this->responseHandler->handle($e->getResponse());
            }
            throw new ApiException(
                'Request failed: '.$e->getMessage(),
                $e->getCode(),
                $e
            );
        } catch (ConnectException $e) {
            throw new ApiException(
                'Failed to connect to Asaas API: '.$e->getMessage(),
                0,
                $e
            );
        } catch (GuzzleException $e) {
            throw new ApiException(
                'HTTP client error: '.$e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}
