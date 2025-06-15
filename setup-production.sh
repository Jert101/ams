#!/bin/bash

# Create necessary directories
mkdir -p public/build/assets

# Set correct permissions
chmod -R 755 public/build
chmod -R 644 public/build/manifest.json
chmod -R 644 public/build/assets/*

# Copy assets to production
cp -r public/build/* /home/vol14_2/infinityfree.com/if0_38972693/htdocs/public/build/

echo "Production setup completed!" 