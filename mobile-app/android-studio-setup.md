# Android Studio Setup Instructions

Follow these steps to correctly configure Android Studio for this project:

## 1. Install JDK 17 or 19 (Recommended)

Download and install JDK 17 or 19 (not higher) from:
- [Eclipse Temurin JDK](https://adoptium.net/temurin/releases/) (recommended)
- [Oracle JDK](https://www.oracle.com/java/technologies/downloads/)

## 2. Configure Gradle JVM in Android Studio

When Android Studio opens:

1. Click on "File" > "Settings" (or "Android Studio" > "Preferences" on macOS)
2. Navigate to "Build, Execution, Deployment" > "Build Tools" > "Gradle"
3. For "Gradle JVM", select your installed JDK 17 or 19 from the dropdown
4. Click "Apply" and "OK"

## 3. Sync the Project

1. Click on the "Sync Project with Gradle Files" button (elephant icon with arrow)
2. Wait for the sync to complete

## 4. If You Still Have Issues

If you continue to see Gradle compatibility errors:

1. Open the `gradle/wrapper/gradle-wrapper.properties` file
2. Make sure the `distributionUrl` is set to `https\://services.gradle.org/distributions/gradle-8.5-bin.zip`
3. Sync the project again

## 5. Building the APK

Once the project is properly configured:

1. Click on "Build" > "Build Bundle(s) / APK(s)" > "Build APK(s)"
2. Wait for the build to complete
3. Android Studio will show a notification when done
4. Click "locate" to find your APK file

The APK file will typically be in: `android/app/build/outputs/apk/debug/app-debug.apk`

Copy this file to your website's download directory to make it available to your users. 