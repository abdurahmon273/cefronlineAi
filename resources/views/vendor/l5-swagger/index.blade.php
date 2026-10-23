<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{config('l5-swagger.documentations.'.$documentation.'.api.title')}}</title>
    <link rel="stylesheet" type="text/css" href="{{ l5_swagger_asset($documentation, 'swagger-ui.css') }}">
    <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation, 'favicon-32x32.png') }}" sizes="32x32"/>
    <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation, 'favicon-16x16.png') }}" sizes="16x16"/>
    <style>
    html
    {
        box-sizing: border-box;
        overflow: -moz-scrollbars-vertical;
        overflow-y: scroll;
    }
    *,
    *:before,
    *:after
    {
        box-sizing: inherit;
    }

    body {
      margin:0;
      background: #fafafa;
    }
    </style>
    @if(config('l5-swagger.defaults.ui.display.dark_mode'))
        <style>
            body#dark-mode,
            #dark-mode .scheme-container {
                background: #1b1b1b;
            }
            #dark-mode .scheme-container,
            #dark-mode .opblock .opblock-section-header{
                box-shadow: 0 1px 2px 0 rgba(255, 255, 255, 0.15);
            }
            #dark-mode .operation-filter-input,
            #dark-mode .dialog-ux .modal-ux,
            #dark-mode input[type=email],
            #dark-mode input[type=file],
            #dark-mode input[type=password],
            #dark-mode input[type=search],
            #dark-mode input[type=text],
            #dark-mode textarea{
                background: #343434;
                color: #e7e7e7;
            }
            #dark-mode .title,
            #dark-mode li,
            #dark-mode p,
            #dark-mode table,
            #dark-mode label,
            #dark-mode .opblock-tag,
            #dark-mode .opblock .opblock-summary-operation-id,
            #dark-mode .opblock .opblock-summary-path,
            #dark-mode .opblock .opblock-summary-path__deprecated,
            #dark-mode h1,
            #dark-mode h2,
            #dark-mode h3,
            #dark-mode h4,
            #dark-mode h5,
            #dark-mode .btn,
            #dark-mode .tab li,
            #dark-mode .parameter__name,
            #dark-mode .parameter__type,
            #dark-mode .prop-format,
            #dark-mode .loading-container .loading:after{
                color: #e7e7e7;
            }
            #dark-mode .opblock-description-wrapper p,
            #dark-mode .opblock-external-docs-wrapper p,
            #dark-mode .opblock-title_normal p,
            #dark-mode .response-col_status,
            #dark-mode table thead tr td,
            #dark-mode table thead tr th,
            #dark-mode .response-col_links,
            #dark-mode .swagger-ui{
                color: wheat;
            }
            #dark-mode .parameter__extension,
            #dark-mode .parameter__in,
            #dark-mode .model-title{
                color: #949494;
            }
            #dark-mode table thead tr td,
            #dark-mode table thead tr th{
                border-color: rgba(120,120,120,.2);
            }
            #dark-mode .opblock .opblock-section-header{
                background: transparent;
            }
            #dark-mode .opblock.opblock-post{
                background: rgba(73,204,144,.25);
            }
            #dark-mode .opblock.opblock-get{
                background: rgba(97,175,254,.25);
            }
            #dark-mode .opblock.opblock-put{
                background: rgba(252,161,48,.25);
            }
            #dark-mode .opblock.opblock-delete{
                background: rgba(249,62,62,.25);
            }
            #dark-mode .loading-container .loading:before{
                border-color: rgba(255,255,255,10%);
                border-top-color: rgba(255,255,255,.6);
            }
            #dark-mode svg:not(:root){
                fill: #e7e7e7;
            }
            #dark-mode .opblock-summary-description {
                color: #fafafa;
            }
        </style>
    @endif
</head>

<body @if(config('l5-swagger.defaults.ui.display.dark_mode')) id="dark-mode" @endif>
<div style="display:flex;justify-content:flex-end;padding:12px 16px 0 16px;background:#fafafa;">
    <form method="POST" action="{{ route('swagger.logout') }}">
        @csrf
        <button type="submit" style="border:none;background:#20a8d8;color:#fff;padding:8px 14px;border-radius:4px;cursor:pointer;">
            Logout
        </button>
    </form>
</div>
<div id="swagger-ui"></div>

<script src="{{ l5_swagger_asset($documentation, 'swagger-ui-bundle.js') }}"></script>
<script src="{{ l5_swagger_asset($documentation, 'swagger-ui-standalone-preset.js') }}"></script>
<script>
    window.onload = function() {
        const tagOrder = {
            'Test':1,
            'Not Auth Pages':2,
            'Auth':3,
            'Profile':4,
            'Courses API': 5,
            'News API':6,
            'Listening API': 7,
            'Reading API': 8,
            'Writing API': 9,
            'Speaking API': 10,
            'Premium API': 11,
        };

        const operationOrder = {
            '/v2/stepPages': 1,

            '/v2/cities/{id}': 101,
            '/v2/regions': 102,

            '/v2/register': 201,
            '/v2/verify_sms': 202,
            '/v2/forgotpassword': 203,
            '/v2/verify_password': 204,
            '/v2/resetPassword': 205,
            '/v2/google/link': 206,

            '/v2/informations': 301,
            '/v2/profile': 302,
            '/v2/logout': 303,
            '/v2/profile-update': 304,
            '/v2/password-update': 305,
            '/v2/delete_account': 306,

            '/v2/courses': 401,
            '/v2/course/show/{id}': 402,
            '/v2/videos/{id}': 403,
            '/v2/comments/{id}': 404,
            '/v2/comments-store/{id}': 405,
            '/v2/view_video': 406,
            '/v2/saved-lessons-store/{id}': 407,
            '/v2/saved-lessons': 408,

            '/v2/newss': 501,
            '/v2/newss/show/{id}': 502,
            '/v2/newss/join/{news}': 503,

            '/v2/listening/exercises': 601,
            '/v2/listening_exercise': 602,
            '/v2/listening_save': 603,
            '/v2/listening/results': 604,
            '/v2/listening/result/show/{id}': 605,

            '/v2/reading/exercises': 701,
            '/v2/reading_exercise': 702,
            '/v2/reading/save': 703,
            '/v2/reading/results': 704,
            '/v2/reading/result/show/{id}': 705,

            '/v2/writing_exercise': 801,
            '/v2/writing/task1/exercises': 802,
            '/v2/writing/task2/exercises': 803,
            '/v2/writing/full/exercises': 804,
            '/v2/writing/task1/save': 805,
            '/v2/writing/task2/save': 806,
            '/v2/writing/full/save': 807,
            '/v2/writing/results': 808,
            '/v2/writing/result/{id}': 809,
            '/v2/writing/saved_result/delete': 810,

            '/v2/speaking_exercise': 901,
            '/v2/speaking/part1/exercises': 902,
            '/v2/speaking/part2/exercises': 903,
            '/v2/speaking/part3/exercises': 904,
            '/v2/speaking/full/exercises': 905,
            '/v2/speaking/save': 906,
            '/v2/speaking/results': 907,
            '/v2/speaking/result/{id}': 908,
            '/v2/speaking/saved_result/delete': 909,

            '/v2/facilities': 1001,
            '/v2/plans': 1002,
            '/v2/Packages': 1003,
            '/v2/checkPromoCode': 1004,
            '/v2/plan/clicked/pay': 1005,
            '/v2/package/clicked/pay': 1006,
            '/v2/check_premium': 1007,
            '/v2/credit/balance': 1008,
        };

        const getPath = function(operation) {
            return operation && typeof operation.get === 'function' ? operation.get('path') : '';
        };

        const getMethod = function(operation) {
            return operation && typeof operation.get === 'function' ? operation.get('method') : '';
        };

        // Build a system
        const ui = SwaggerUIBundle({
            dom_id: '#swagger-ui',
            url: "{!! $urlToDocs !!}",
            operationsSorter: function(a, b) {
                const pathA = getPath(a);
                const pathB = getPath(b);
                const orderA = Object.prototype.hasOwnProperty.call(operationOrder, pathA) ? operationOrder[pathA] : 999;
                const orderB = Object.prototype.hasOwnProperty.call(operationOrder, pathB) ? operationOrder[pathB] : 999;

                if (orderA !== orderB) {
                    return orderA - orderB;
                }

                if (pathA !== pathB) {
                    return pathA.localeCompare(pathB);
                }

                return getMethod(a).localeCompare(getMethod(b));
            },
            tagsSorter: function(a, b) {
                const orderA = Object.prototype.hasOwnProperty.call(tagOrder, a) ? tagOrder[a] : 999;
                const orderB = Object.prototype.hasOwnProperty.call(tagOrder, b) ? tagOrder[b] : 999;

                if (orderA !== orderB) {
                    return orderA - orderB;
                }

                return a.localeCompare(b);
            },
            configUrl: {!! isset($configUrl) ? '"' . $configUrl . '"' : 'null' !!},
            validatorUrl: {!! isset($validatorUrl) ? '"' . $validatorUrl . '"' : 'null' !!},
            oauth2RedirectUrl: "{{ route('l5-swagger.'.$documentation.'.oauth2_callback', [], $useAbsolutePath) }}",

            requestInterceptor: function(request) {
                request.headers['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
                return request;
            },

            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],

            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],

            layout: "StandaloneLayout",
            docExpansion : "{!! config('l5-swagger.defaults.ui.display.doc_expansion', 'none') !!}",
            deepLinking: true,
            filter: {!! config('l5-swagger.defaults.ui.display.filter') ? 'true' : 'false' !!},
            persistAuthorization: "{!! config('l5-swagger.defaults.ui.authorization.persist_authorization') ? 'true' : 'false' !!}",

        })

        window.ui = ui

        @if(in_array('oauth2', array_column(config('l5-swagger.defaults.securityDefinitions.securitySchemes'), 'type')))
        ui.initOAuth({
            usePkceWithAuthorizationCodeGrant: "{!! (bool)config('l5-swagger.defaults.ui.authorization.oauth2.use_pkce_with_authorization_code_grant') !!}"
        })
        @endif
    }
</script>
</body>
</html>
