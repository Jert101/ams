@echo off
echo Opening Android Studio with the CKP-KofA Network project...
echo.
echo IMPORTANT: When Android Studio opens, follow these steps:
echo 1. Go to File ^> Settings ^> Build, Execution, Deployment ^> Build Tools ^> Gradle
echo 2. Set Gradle JVM to JDK 17 or 19 (not higher)
echo 3. Click Apply and OK
echo 4. Click "Sync Project with Gradle Files" button
echo.
echo Press any key to open Android Studio...
pause > nul

cd android
start . 