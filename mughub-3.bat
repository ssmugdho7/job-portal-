@echo off
setlocal enabledelayedexpansion

:: Set colors and title
color 0A
title Git Repository Manager

:: ==========================================================
:: INITIAL SETUP - Repository selection
:: ==========================================================
:SETUP
cls
echo #############################################
echo #       Git Repository Manager - Setup       #
echo #############################################
echo.
set /p repo_input=Enter local repo path OR GitHub URL: 

if "!repo_input!"=="" (
    echo Error: Repository cannot be empty!
    pause
    goto SETUP
)

:: Detect if input looks like a GitHub URL
echo "!repo_input!" | findstr /i "https://github.com/" >nul
if !errorlevel! equ 0 (
    set repo_url=!repo_input!
    set repo_dir=repo_temp

    if not exist "!repo_dir!" (
        echo Cloning repository into folder "!repo_dir!"...
        git clone "!repo_url!" "!repo_dir!"
    )
    cd /d "!repo_dir!"
) else (
    :: Assume it's a local directory
    if exist "!repo_input!" (
        cd /d "!repo_input!"
    ) else (
        echo Error: Local path does not exist!
        pause
        goto SETUP
    )
)

:: Verify it’s a Git repo
git rev-parse --is-inside-work-tree >nul 2>&1
if %errorlevel% neq 0 (
    echo Error: This is not a valid Git repository!
    pause
    goto SETUP
)

:: ==========================================================
:: MAIN MENU
:: ==========================================================
:MENU
cls
echo #############################################
echo #        Git Repository Manager Menu        #
echo #############################################
echo.
echo Current repo: %cd%
echo.
echo 1. Push changes to GitHub
echo 2. Merge one branch into another (auto "pull request")
echo 3. Create a new branch
echo 4. Exit
echo.
set /p choice=Choose an option [1-4]: 

if "%choice%"=="1" goto PUSH
if "%choice%"=="2" goto MERGE
if "%choice%"=="3" goto CREATE
if "%choice%"=="4" exit /b 0
goto MENU

:: ==========================================================
:: PUSH
:: ==========================================================
:PUSH
cls
echo #############################################
echo #                Git Push                   #
echo #############################################
echo.

:: Ask for commit message
set /p commit_msg=Enter your commit message [default: Initial commit]: 
if "!commit_msg!"=="" set commit_msg=Initial commit

echo Adding files to staging area...
git add .
git commit -m "!commit_msg!"

echo Ensuring branch is 'main'...
git branch -M main 2>nul

:: Ensure remote origin
git remote -v | findstr "origin" >nul
if %errorlevel% neq 0 (
    if "!repo_url!"=="" (
        set /p repo_url=Enter GitHub repository URL: 
    )
    git remote add origin "!repo_url!"
)

echo Pushing to GitHub...
git push -u origin main

echo.
echo Push completed!
pause
goto MENU

:: ==========================================================
:: MERGE (auto "pull request" behavior + force overwrite)
:: ==========================================================
:MERGE
cls
echo #############################################
echo #       Merge One Branch Into Another       #
echo #############################################
echo.
git fetch --all

echo Available branches:
for /f "tokens=*" %%b in ('git branch -r') do echo   %%b

echo.
set /p source_branch=Enter source branch (take changes from): 
if "!source_branch!"=="" (
    echo Error: Source branch cannot be empty!
    pause
    goto MENU
)

set /p target_branch=Enter target branch (dump changes into): 
if "!target_branch!"=="" (
    echo Error: Target branch cannot be empty!
    pause
    goto MENU
)

echo.
echo Choose merge mode:
echo 1. Normal merge (preserve history)
echo 2. Force overwrite (make target EXACT copy of source)
set /p merge_mode=Select [1-2]: 

:: Checkout target
echo Checking out target branch: !target_branch!
git checkout !target_branch! 2>nul || git checkout -b !target_branch! origin/!target_branch!

if "!merge_mode!"=="2" (
    echo.
    echo FORCE OVERWRITE MODE ENABLED!
    echo WARNING: This will make target identical to source and discard its history changes.
    pause
    git fetch origin !source_branch!
    git reset --hard origin/!source_branch!
    git push origin !target_branch! --force
    echo.
    echo Target branch "!target_branch!" is now an exact copy of "!source_branch!".
) else (
    echo.
    echo Normal merge selected...
    git pull origin !target_branch!
    git merge origin/!source_branch! --no-edit
    git push origin !target_branch!
    echo.
    echo Branch "!source_branch!" successfully merged into "!target_branch!".
)

pause
goto MENU

:: ==========================================================
:: CREATE BRANCH
:: ==========================================================
:CREATE
cls
echo #############################################
echo #           Create New Branch               #
echo #############################################
echo.
set /p new_branch=Enter new branch name: 
if "!new_branch!"=="" (
    echo Error: Branch name cannot be empty!
    pause
    goto MENU
)

echo Creating and switching to branch "!new_branch!"...
git checkout -b "!new_branch!"

:: Ask to push immediately
set /p push_now=Do you want to push this branch to origin? (y/n): 
if /i "!push_now!"=="y" (
    git push -u origin "!new_branch!"
    echo Branch pushed to GitHub.
)

echo.
echo Branch "!new_branch!" created and checked out.
pause
goto MENU
