*** Settings ***
Documentation     Test edit oauth2 clients
Library           SeleniumLibrary

*** Variables ***
${CLIENTS_REGISTRY_URL}    http://127.0.0.1:8000/simplesamlphp/module.php/oauth2/registry
${NEW_CLIENT_URL}    http://127.0.0.1:8000/simplesamlphp/module.php/oauth2/registry/new
${EDIT_CLIENT_URL}    http://127.0.0.1:8000/simplesamlphp/module.php/oauth2/registry/edit

${BROWSER}        Chrome

*** Test Cases ***
Valid Login
    Open Browser To Clients Registry Page
    Input Password    1234
    Submit Credentials

    Navigate To New Client Page
    Fill Private Client Auth Default
    Submit Client Form
    Log Location
    Element Text Should Be    tag:td    demo_private
    Location Should Be    ${CLIENTS_REGISTRY_URL}

    Navigate To New Client Page
    Fill Public Client Auth Default
    Submit Client Form
    Location Should Be    ${CLIENTS_REGISTRY_URL}

    Navigate To New Client Page
    Fill Private Client Custom Auth
    Submit Client Form
    Location Should Be    ${CLIENTS_REGISTRY_URL}

    #Welcome Page Should Be Open
    [Teardown]    Close Browser

*** Keywords ***
Open Browser To Clients Registry Page
    Open Browser    ${CLIENTS_REGISTRY_URL}    ${BROWSER}
    # Assert redirect to login page
    #Title Should Be    Login Page

Navigate To New Client Page
    Go To    ${NEW_CLIENT_URL}

Input Username
    [Arguments]    ${username}
    Input Text    username_field    ${username}

Input Password
    [Arguments]    ${password}
    Input Text    password    ${password}

Submit Credentials
    Click Button    submit_button

Submit Client Form
    Click Button    _submit

Fill Private Client Auth Default
    Input Text                  frm-client-name             demo_private
    Input Text                  frm-client-description      demo_private_description
    Input Text                  frm-client-scopes           basic
    Input Text                  frm-client-redirect_uri     http://127.0.0.1:8000/dummyclient/oauth2/endpoint.php
    Select Checkbox             frm-client-is_confidential

Fill Public Client Auth Default
    Input Text                  frm-client-name             demo_public
    Input Text                  frm-client-description      demo_public_description
    Input Text                  frm-client-scopes           basic
    Input Text                  frm-client-redirect_uri     http://127.0.0.1:8000/dummyclient/oauth2/endpoint.php
    Unselect Checkbox           frm-client-is_confidential

Fill Private Client Custom Auth
    Input Text                  frm-client-name             demo_private_custom
    Input Text                  frm-client-description      demodescription
    Input Text                  frm-client-scopes           basic extra
    Input Text                  frm-client-redirect_uri     http://127.0.0.1:8000/dummyclient/oauth2/endpoint.php
    Select From List By Value   frm-client-auth_source      demo2
    Select Checkbox             frm-client-is_confidential

Welcome Page Should Be Open
    Title Should Be    Welcome Page

