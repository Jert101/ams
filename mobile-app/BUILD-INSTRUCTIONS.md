# How to Build the CKP-KofA Network Mobile App

Follow these step-by-step instructions to build the APK file for your mobile app.

## Prerequisites
1. Make sure Android Studio is installed and running
2. Make sure JDK 17 is installed (download from https://adoptium.net/temurin/releases/?version=17)

## Steps to Build the APK

### 1. Fix the Gradle JDK Issue
1. In Android Studio, go to **File > Settings** (or **Android Studio > Preferences** on Mac)
2. Navigate to **Build, Execution, Deployment > Build Tools > Gradle**
3. For "Gradle JDK", click on the dropdown and select "Download JDK..."
4. In the popup dialog:
   - Version: Select **17**
   - Vendor: Select **Eclipse Temurin (AdoptOpenJDK HotSpot)**
   - Click **Download**
5. Wait for the download to complete
6. Click **Apply** and then **OK**

### 2. Sync the Project
1. Click on the **Sync Project with Gradle Files** button (elephant icon with arrow in the toolbar)
2. Wait for the sync to complete successfully

### 3. Build the Debug APK
1. Click on **Build** in the top menu
2. Select **Build Bundle(s) / APK(s)**
3. Choose **Build APK(s)**
4. Wait for the build to complete
5. Android Studio will show a notification when done
6. Click **locate** to find your APK file

### 4. Create a Release APK (for distribution)
1. Click on **Build** in the top menu
2. Select **Generate Signed Bundle / APK**
3. Select **APK** and click **Next**
4. Create a new keystore:
   - Click **Create new...**
   - Fill in the keystore information:
     - Keystore path: Choose a location to save it
     - Password: Create a strong password (remember it!)
     - Key alias: "ckp-kofa-key"
     - Key password: Create another password or use the same one
     - Certificate information: Fill in your details
   - Click **OK**
5. Make sure your keystore information is filled in
6. Click **Next**
7. Select **release** build variant
8. Click **Finish**
9. Wait for the build to complete

### 5. Deploy the APK to Your Website
1. Find the generated APK:
   - Debug APK: `app/build/outputs/apk/debug/app-debug.apk`
   - Release APK: `app/build/outputs/apk/release/app-release.apk`
2. Copy the APK to your website's download directory:
   - `C:\xampp\htdocs\ams\public\downloads\ckp-kofa-app.apk`

## Troubleshooting
- If you see "Unsupported class file major version 65" error, it means you're using Java 21 which is too new. Make sure to use Java 17 as instructed above.
- If the build fails, check the "Build" tab at the bottom of Android Studio for detailed error messages.
- Make sure your `gradle-wrapper.properties` file specifies Gradle version 8.5 or higher. 