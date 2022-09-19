*** Settings ***
Documentation     Test OAuth2 flow
Library           SeleniumLibrary

*** Variables ***
${CLIENT_ENTRY_POINT_URL}    http://127.0.0.1:8000/dummyclient
${CLIENT_REDIRECT_ENDPOINT_URL}    http://127.0.0.1:8000/dummyclient/oauth2/endpoint.php
${SERVER_TOKEN_ENDPOINT_URL}    http://127.0.0.1:8000/simplesamlphp/module.php/oauth2/access_token

${BROWSER}        Chrome

*** Test Cases ***
Allow Auth Code Grant
    Open Browser To Client Entry Point Page
    Initiate AuthCodeGrant Procedure
    Perform Login
    Allow Scopes

    Location Should Contain    ${CLIENT_REDIRECT_ENDPOINT_URL}
    Element Should Not Contain    name:token    null
    Element Should Not Contain    name:userInfo    null
    Element Should Not Contain    name:refreshedToken    null
    Element Should Contain    name:error    null

    [Teardown]    Close Browser

Deny Auth Code Grant
    Open Browser To Client Entry Point Page
    Initiate AuthCodeGrant Procedure
    Perform Login
    Deny Scopes

    Location Should Contain    ${CLIENT_REDIRECT_ENDPOINT_URL}
    Element Should Contain    name:token    null
    Element Should Contain    name:userInfo    null
    Element Should Contain    name:refreshedToken    null
    Element Should Not Contain    name:error    null

    [Teardown]    Close Browser

Client Credentials Token
    Open Browser To Client Entry Point Page
    Initiate ClientCredentials Procedure

    Location Should Contain    ${SERVER_TOKEN_ENDPOINT_URL}
    Page Should Contain    "token_type":"Bearer"

    [Teardown]    Close Browser


*** Keywords ***
Open Browser To Client Entry Point Page
    Open Browser    ${CLIENT_ENTRY_POINT_URL}    ${BROWSER}

Navigate To New Client Page
    Go To    ${NEW_CLIENT_URL}

Perform Login
    Input Username    student1
    Input Password    student1pass
    Submit Credentials

Input Username
    [Arguments]    ${username}
    Input Text    username    ${username}

Input Password
    [Arguments]    ${password}
    Input Text    password    ${password}

Initiate AuthCodeGrant Procedure
    Click Button    name:auth_code

Initiate ClientCredentials Procedure
    Click Button    name:client_credentials

Allow Scopes
    Click Button    allow

Deny Scopes
    Click Button    deny

Submit Credentials
    Click Button    submit_button

Welcome Page Should Be Open
    Title Should Be    Welcome Page

