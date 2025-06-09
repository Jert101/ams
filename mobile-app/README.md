# CKP-KofA Network Mobile App

This directory contains the necessary files to build native mobile applications for Android and iOS using Capacitor.

## Prerequisites

- [Node.js](https://nodejs.org/) (v14 or higher)
- [Android Studio](https://developer.android.com/studio) (for Android builds)
- [Xcode](https://developer.apple.com/xcode/) (for iOS builds, Mac only)
- [Java Development Kit (JDK)](https://www.oracle.com/java/technologies/javase-jdk11-downloads.html) (for Android builds)

## Setup

1. Navigate to this directory:
   ```
   cd mobile-app
   ```

2. Install dependencies:
   ```
   npm install
   ```

3. Add platforms:
   ```
   npm run add-android    # For Android
   npm run add-ios        # For iOS (Mac only)
   ```

## Building the App

1. Build the app:
   ```
   npm run build
   ```

2. Open in Android Studio or Xcode:
   ```
   npm run open-android    # For Android
   npm run open-ios        # For iOS (Mac only)
   ```

3. Build and run the app from Android Studio or Xcode.

## Creating a Release Build

### Android

1. Open the project in Android Studio:
   ```
   npm run open-android
   ```

2. From Android Studio, select `Build > Generate Signed Bundle / APK`.

3. Follow the wizard to create a signed APK or App Bundle.

4. The resulting APK or AAB file can be distributed to users for installation.

### iOS (Mac only)

1. Open the project in Xcode:
   ```
   npm run open-ios
   ```

2. In Xcode, select `Product > Archive`.

3. Follow the distribution process in the Organizer window.

## Customization

- App icon and splash screen images are located in the respective platform folders.
- To update the app name or bundle ID, modify the `capacitor.config.json` file.

## Troubleshooting

- If you encounter any issues, make sure you have the correct versions of Android Studio, Xcode, and JDK installed.
- Check the Capacitor documentation for more information: [https://capacitorjs.com/docs](https://capacitorjs.com/docs) 