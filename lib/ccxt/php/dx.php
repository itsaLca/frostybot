<?php

namespace ccxt;

// PLEASE DO NOT EDIT THIS FILE, IT IS GENERATED AND WILL BE OVERWRITTEN:
// https://github.com/ccxt/ccxt/blob/master/CONTRIBUTING.md#how-to-contribute-code

use Exception; // a common import

class dx extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'dx',
            'name' => 'DX.Exchange',
            'countries' => array ( 'GB', 'EU' ),
            'rateLimit' => 1500,
            'version' => 'v1',
            'has' => array (
                'cancelAllOrders' => false,
                'cancelOrder' => true,
                'cancelOrders' => false,
                'CORS' => false,
                'createDepositAddress' => false,
                'createLimitOrder' => true,
                'createMarketOrder' => true,
                'createOrder' => true,
                'deposit' => false,
                'editOrder' => false,
                'fetchBalance' => true,
                'fetchBidsAsks' => false,
                'fetchClosedOrders' => true,
                'fetchCurrencies' => false,
                'fetchDepositAddress' => false,
                'fetchDeposits' => false,
                'fetchFundingFees' => false,
                'fetchL2OrderBook' => false,
                'fetchLedger' => false,
                'fetchMarkets' => true,
                'fetchMyTrades' => false,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => false,
                'fetchOrderBook' => true,
                'fetchOrderBooks' => false,
                'fetchOrders' => false,
                'fetchTicker' => true,
                'fetchTickers' => false,
                'fetchTrades' => false,
                'fetchTradingFee' => false,
                'fetchTradingFees' => false,
                'fetchTradingLimits' => false,
                'fetchTransactions' => false,
                'fetchWithdrawals' => false,
                'privateAPI' => true,
                'publicAPI' => true,
                'withdraw' => false,
            ),
            'timeframes' => array (
                '1m' => '1m',
                '5m' => '5m',
                '1h' => '1h',
                '1d' => '1d',
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/57979980-6483ff80-7a2d-11e9-9224-2aa20665703b.jpg',
                'api' => 'https://acl.dx.exchange',
                'www' => 'https://dx.exchange',
                'doc' => 'https://apidocs.dx.exchange',
                'fees' => 'https://dx.exchange/fees',
                'referral' => 'https://dx.exchange/registration?dx_cid=20&dx_scname=100001100000038139',
            ),
            'requiredCredentials' => array (
                'apiKey' => true,
                'secret' => false,
            ),
            'fees' => array (
                'trading' => array (
                    'tierBased' => true,
                    'percentage' => true,
                    'taker' => 0.25 / 100,
                    'maker' => 0.25 / 100,
                    'tiers' => array (
                        'taker' => [
                            [0, 0.25 / 100],
                            [1000000, 0.2 / 100],
                            [5000000, 0.15 / 100],
                            [10000000, 0.1 / 100],
                        ],
                        'maker' => [
                            [0, 0.25 / 100],
                            [1000000, 0.2 / 100],
                            [5000000, 0.15 / 100],
                            [10000000, 0.1 / 100],
                        ],
                    ),
                ),
                'funding' => array (
                ),
            ),
            'exceptions' => array (
                'exact' => array (
                    'EOF' => '\\ccxt\\BadRequest',
                ),
                'broad' => array (
                    'json => cannot unmarshal object into Go value of type' => '\\ccxt\\BadRequest',
                    'not allowed to cancel this order' => '\\ccxt\\BadRequest',
                    'request timed out' => '\\ccxt\\RequestTimeout',
                    'balance_freezing.freezing validation.balance_freeze' => '\\ccxt\\InsufficientFunds',
                    'order_creation.validation.validation' => '\\ccxt\\InvalidOrder',
                ),
            ),
            'api' => array (
                'public' => array (
                    'post' => array (
                        'AssetManagement.GetInstruments',
                        'AssetManagement.GetTicker',
                        'AssetManagement.History',
                        'Authorization.LoginByToken',
                        'OrderManagement.GetOrderBook',
                    ),
                ),
                'private' => array (
                    'post' => array (
                        'Balance.Get',
                        'OrderManagement.Cancel',
                        'OrderManagement.Create',
                        'OrderManagement.OpenOrders',
                        'OrderManagement.OrderHistory',
                    ),
                ),
            ),
            'commonCurrencies' => array (
                'BCH' => 'Bitcoin Cash',
            ),
            'precisionMode' => DECIMAL_PLACES,
            'options' => array (
                'orderTypes' => array (
                    'market' => 1,
                    'limit' => 2,
                ),
                'orderSide' => array (
                    'buy' => 1,
                    'sell' => 2,
                ),
            ),
        ));
    }

    public function number_to_object ($number) {
        $string = $this->decimal_to_precision($number, ROUND, 10, DECIMAL_PLACES, NO_PADDING);
        $decimals = $this->precision_from_string($string);
        $valueStr = str_replace('.', '', $string);
        return array (
            'value' => $this->safe_integer(array( 'a' => $valueStr ), 'a', null),
            'decimals' => $decimals,
        );
    }

    public function object_to_number ($obj) {
        $value = $this->decimal_to_precision($obj['value'], ROUND, 0, DECIMAL_PLACES, NO_PADDING);
        $decimals = $this->decimal_to_precision(-$obj['decimals'], ROUND, 0, DECIMAL_PLACES, NO_PADDING);
        return $this->safe_float(array (
            'a' => $value . 'e' . $decimals,
        ), 'a', null);
    }

    public function fetch_markets ($params = array ()) {
        $markets = $this->publicPostAssetManagementGetInstruments ($params);
        $instruments = $markets['result']['instruments'];
        $result = array();
        for ($i = 0; $i < count ($instruments); $i++) {
            $instrument = $instruments[$i];
            $id = $this->safe_string($instrument, 'id');
            $numericId = $this->safe_integer($instrument, 'id');
            $asset = $this->safe_value($instrument, 'asset', array());
            $fullName = $this->safe_string($asset, 'fullName');
            list($base, $quote) = explode('/', $fullName);
            $amountPrecision = 0;
            if ($instrument['meQuantityMultiplier'] !== 0) {
                $amountPrecision = intval (log10 ($instrument['meQuantityMultiplier']));
            }
            $base = $this->safe_currency_code($base);
            $quote = $this->safe_currency_code($quote);
            $baseId = $this->safe_string($asset, 'baseCurrencyId');
            $quoteId = $this->safe_string($asset, 'quotedCurrencyId');
            $baseNumericId = $this->safe_integer($asset, 'baseCurrencyId');
            $quoteNumericId = $this->safe_integer($asset, 'quotedCurrencyId');
            $symbol = $base . '/' . $quote;
            $result[] = array (
                'id' => $id,
                'numericId' => $numericId,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'baseNumericId' => $baseNumericId,
                'quoteNumericId' => $quoteNumericId,
                'info' => $instrument,
                'precision' => array (
                    'amount' => $amountPrecision,
                    'price' => $this->safe_integer($asset, 'tailDigits'),
                ),
                'limits' => array (
                    'amount' => array (
                        'min' => $this->safe_float($instrument, 'minOrderQuantity'),
                        'max' => $this->safe_float($instrument, 'maxOrderQuantity'),
                    ),
                    'price' => array (
                        'min' => 0,
                        'max' => null,
                    ),
                    'cost' => array (
                        'min' => 0,
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function parse_ticker ($ticker, $market = null) {
        $tickerKeys = is_array($ticker) ? array_keys($ticker) : array();
        // Python needs an integer to access $this->markets_by_id
        // and a string to access the $ticker object
        $tickerKey = $tickerKeys[0];
        $instrumentId = $this->safe_integer(array( 'a' => $tickerKey ), 'a');
        $ticker = $ticker[$tickerKey];
        $symbol = $this->markets_by_id[$instrumentId]['symbol'];
        $last = $this->safe_float($ticker, 'last');
        $timestamp = $this->safe_integer($ticker, 'time') / 1000;
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => $this->safe_float($ticker, 'high24'),
            'low' => $this->safe_float($ticker, 'low24'),
            'bid' => null,
            'bidVolume' => null,
            'ask' => null,
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $this->safe_float($ticker, 'change'),
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'volume24'),
            'quoteVolume' => $this->safe_float($ticker, 'volume24converted'),
            'info' => $ticker,
        );
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'instrumentIds' => [ $market['numericId'] ],
            'currencyId' => $market['quoteNumericId'],
        );
        $response = $this->publicPostAssetManagementGetTicker (array_merge ($request, $params));
        return $this->parse_ticker($response['result']['tickers'], $market);
    }

    public function parse_ohlcv ($ohlcv, $market = null, $timeframe = '1m', $since = null, $limit = null) {
        //
        //     {
        //         "date":1546878960,
        //         "open":0.038064,
        //         "high":0.038064,
        //         "low":0.038064,
        //         "close":0.038064,
        //         "volume":0.00755418,
        //         "id":169042,
        //         "instrumentId":1015,
        //         "type":"1m"
        //     }
        //
        return array (
            $this->safe_timestamp($ohlcv, 'date'),
            $this->safe_float($ohlcv, 'open'),
            $this->safe_float($ohlcv, 'high'),
            $this->safe_float($ohlcv, 'low'),
            $this->safe_float($ohlcv, 'close'),
            $this->safe_float($ohlcv, 'volume'),
        );
    }

    public function fetch_ohlcv ($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'timestampFrom' => $since,
            'timestampTill' => null,
            'instrumentId' => $market['numericId'],
            'type' => $this->timeframes[$timeframe],
            'pagination' => array (
                'limit' => $limit,
                'offset' => 0,
            ),
        );
        $response = $this->publicPostAssetManagementHistory (array_merge ($request, $params));
        //
        //     {
        //         "id":"1.565248994048e+12",
        //         "result":{
        //             "assets":array (
        //                 array("date":1546878960,"open":0.038064,"high":0.038064,"low":0.038064,"close":0.038064,"volume":0.00755418,"id":169042,"instrumentId":1015,"type":"1m"),
        //                 array("date":1546878660,"open":0.037863,"high":0.037863,"low":0.037863,"close":0.037863,"volume":0.0075726,"id":169028,"instrumentId":1015,"type":"1m"),
        //                 array("date":1546860360,"open":0.03864,"high":0.03864,"low":0.03864,"close":0.03864,"volume":0.0013524,"id":168924,"instrumentId":1015,"type":"1m"),
        //                 array("date":1546848480,"open":0.038969,"high":0.038969,"low":0.038969,"close":0.038969,"volume":0.01654819,"id":168880,"instrumentId":1015,"type":"1m"),
        //             ),
        //             "total":array (
        //                 "count":52838
        //             }
        //         ),
        //         "error":null
        //     }
        //
        return $this->parse_ohlcvs($response['result']['assets'], $market, $timeframe, $since, $limit);
    }

    public function fetch_open_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'pagination' => array (
                'limit' => $limit,
                'offset' => 0,
            ),
        );
        $market = null;
        if ($symbol !== null) {
            $market = $this->market ($symbol);
            $request['instrumentId'] = $market['numericId'];
        }
        $response = $this->privatePostOrderManagementOpenOrders (array_merge ($request, $params));
        return $this->parse_orders($response['result']['orders'], $market, $since, $limit);
    }

    public function fetch_closed_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'pagination' => array (
                'limit' => $limit,
                'offset' => 0,
            ),
        );
        $market = null;
        if ($symbol !== null) {
            $market = $this->market ($symbol);
            $request['instrumentId'] = $market['numericId'];
        }
        $response = $this->privatePostOrderManagementOrderHistory (array_merge ($request, $params));
        return $this->parse_orders($response['result']['ordersForHistory'], $market, $since, $limit);
    }

    public function parse_order ($order, $market = null) {
        $orderStatusMap = array (
            '1' => 'open',
        );
        $innerOrder = $this->safe_value($order, 'order', null);
        if ($innerOrder !== null) {
            // fetchClosedOrders returns orders in an extra object
            $order = $innerOrder;
            $orderStatusMap = array (
                '1' => 'closed',
                '2' => 'canceled',
            );
        }
        $side = 'buy';
        if ($order['direction'] === $this->options['orderSide']['sell']) {
            $side = 'sell';
        }
        $status = null;
        $orderStatus = $this->safe_string($order, 'status', null);
        if (is_array($orderStatusMap) && array_key_exists($orderStatus, $orderStatusMap)) {
            $status = $orderStatusMap[$orderStatus];
        }
        $marketId = $this->safe_string($order, 'instrumentId');
        $symbol = null;
        if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
            $market = $this->markets_by_id[$marketId];
            $symbol = $market['symbol'];
        }
        $orderType = 'limit';
        if ($order['orderType'] === $this->options['orderTypes']['market']) {
            $orderType = 'market';
        }
        $timestamp = $this->safe_timestamp($order, 'time');
        $quantity = $this->object_to_number ($order['quantity']);
        $filledQuantity = $this->object_to_number ($order['filledQuantity']);
        $id = $this->safe_string($order, 'externalOrderId');
        return array (
            'info' => $order,
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'lastTradeTimestamp' => null,
            'symbol' => $symbol,
            'type' => $orderType,
            'side' => $side,
            'price' => $this->object_to_number ($order['price']),
            'average' => null,
            'amount' => $quantity,
            'remaining' => $quantity - $filledQuantity,
            'filled' => $filledQuantity,
            'status' => $status,
            'fee' => null,
        );
    }

    public function parse_bid_ask ($bidask, $priceKey = 0, $amountKey = 1) {
        $price = $this->object_to_number ($bidask[$priceKey]);
        $amount = $this->object_to_number ($bidask[$amountKey]);
        return array ( $price, $amount );
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'instrumentId' => $market['numericId'],
        );
        $response = $this->publicPostOrderManagementGetOrderBook (array_merge ($request, $params));
        $orderbook = $this->safe_value($response, 'result');
        return $this->parse_order_book($orderbook, null, 'sell', 'buy', 'price', 'qty');
    }

    public function sign_in ($params = array ()) {
        $this->check_required_credentials();
        $request = array (
            'token' => $this->apiKey,
            'secret' => $this->secret,
        );
        $response = $this->publicPostAuthorizationLoginByToken (array_merge ($request, $params));
        $expiresIn = $response['result']['expiry'];
        $this->options['expires'] = $this->sum ($this->milliseconds (), $expiresIn * 1000);
        $this->options['accessToken'] = $response['result']['token'];
        return $response;
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostBalanceGet ($params);
        $result = array( 'info' => $response );
        $balances = $this->safe_value($response['result'], 'balance');
        $currencyIds = is_array($balances) ? array_keys($balances) : array();
        for ($i = 0; $i < count ($currencyIds); $i++) {
            $currencyId = $currencyIds[$i];
            $balance = $this->safe_value($balances, $currencyId, array());
            $code = $this->safe_currency_code($currencyId);
            $account = array (
                'free' => $this->safe_float($balance, 'available'),
                'used' => $this->safe_float($balance, 'frozen'),
                'total' => $this->safe_float($balance, 'total'),
            );
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $direction = $this->safe_integer($this->options['orderSide'], $side);
        $market = $this->market ($symbol);
        $order = array (
            'direction' => $direction,
            'instrumentId' => $market['numericId'],
            'orderType' => 2,
            'quantity' => $this->number_to_object ($amount),
        );
        $order['orderType'] = $this->options['orderTypes'][$type];
        if ($type === 'limit') {
            $order['price'] = $this->number_to_object ($price);
        }
        $request = array (
            'order' => $order,
        );
        $result = $this->privatePostOrderManagementCreate (array_merge ($request, $params));
        // todo => rewrite for parseOrder
        return array (
            'info' => $result,
            'id' => $result['result']['externalOrderId'],
        );
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $request = array( 'externalOrderId' => $id );
        return $this->privatePostOrderManagementCancel (array_merge ($request, $params));
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        if (gettype ($params) === 'array' && count (array_filter (array_keys ($params), 'is_string')) == 0) {
            $arrayLength = is_array ($params) ? count ($params) : 0;
            if ($arrayLength === 0) {
                // In PHP $params = array () causes this to fail, because
                // the API requests an object, not an array, even if it is empty
                $params = array( '__associative' => true );
            }
        }
        $parameters = array (
            'jsonrpc' => '2.0',
            'id' => $this->milliseconds (),
            'method' => $path,
            'params' => [$params],
        );
        $url = $this->urls['api'];
        $headers = array( 'Content-Type' => 'application/json-rpc' );
        if ($method === 'GET') {
            if ($parameters) {
                $url .= '?' . $this->urlencode ($parameters);
            }
        } else {
            $body = $this->json ($parameters);
        }
        if ($api === 'private') {
            $token = $this->safe_string($this->options, 'accessToken');
            if ($token === null) {
                throw new AuthenticationError($this->id . ' ' . $path . ' endpoint requires a prior call to signIn() method');
            }
            $expires = $this->safe_integer($this->options, 'expires');
            if ($expires !== null) {
                if ($this->milliseconds () >= $expires) {
                    throw new AuthenticationError($this->id . ' accessToken expired, call signIn() method');
                }
            }
            $headers['Authorization'] = $token;
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors ($httpCode, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if (!$response) {
            return; // fallback to default $error handler
        }
        $error = $response['error'];
        if ($error) {
            $feedback = $this->id . ' ' . $this->json ($response);
            $exact = $this->exceptions['exact'];
            if (is_array($exact) && array_key_exists($error, $exact)) {
                throw new $exact[$error]($feedback);
            }
            $broad = $this->exceptions['broad'];
            $broadKey = $this->findBroadlyMatchedKey ($broad, $error);
            if ($broadKey !== null) {
                throw new $broad[$broadKey]($feedback);
            }
            throw new ExchangeError($feedback); // unknown $error
        }
    }
}