@echo off
echo ===================================
echo CKP-KofA App Quick Fix - Stuck Loading Issue
echo ===================================

echo Setting JAVA_HOME to JDK 17...
set JAVA_HOME=C:\Program Files\Eclipse Adoptium\jdk-17.0.15.6-hotspot
echo JAVA_HOME set to: %JAVA_HOME%

echo.
echo Building the debug APK with fixed redirect...
cd android
call ./gradlew assembleDebug

echo.
echo Checking if the APK was built successfully...
if exist app\build\outputs\apk\debug\app-debug.apk (
  echo APK built successfully!
  echo Location: %CD%\app\build\outputs\apk\debug\app-debug.apk
  
  echo.
  echo Copying APK to downloads directory...
  copy app\build\outputs\apk\debug\app-debug.apk ..\..\public\downloads\ckp-kofa-app.apk
  echo APK copied to: ..\..\public\downloads\ckp-kofa-app.apk
) else (
  echo APK build failed or not found at the expected location.
  echo Please check the build logs for errors.
)

cd ..
echo.
echo Build process completed.
pause 