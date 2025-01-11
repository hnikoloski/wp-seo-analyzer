# 🔍 WP SEO Analyzer

> 🚀 Supercharge your WordPress content analysis with this high-performance SEO toolkit!

[![WordPress](https://img.shields.io/badge/WordPress-6.7.1%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-8.3.3%2B-purple.svg)](https://php.net/)
[![Node](https://img.shields.io/badge/Node-20.14.0%2B-green.svg)](https://nodejs.org/)
[![License](https://img.shields.io/badge/license-GPL--2.0-orange.svg)](LICENSE)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](CONTRIBUTING.md)

WP SEO Analyzer is a powerful, modern WordPress plugin that brings enterprise-level SEO analysis capabilities to your fingertips. Built with performance and user experience in mind, it helps you optimize your content with real-time analysis and actionable insights.

![WP SEO Analyzer Demo](screenshot.png)

## ✨ Key Features

### 📊 Smart Content Analysis
- 📝 Intelligent word count calculation
- 🎯 Precise keyword density analysis
- 🔄 Real-time content processing
- 📚 Support for all public post types

### 🎨 Modern User Interface
- ⚡️ Lightning-fast, responsive design
- 📱 Mobile-optimized experience
- 🔍 Advanced filtering capabilities
- 📊 Interactive data tables
- 📄 Smart pagination for large datasets

### 🔌 Developer-Friendly API
- 🔒 Secure REST API endpoints
- 📡 Clean JSON responses
- 🛡️ Built-in authentication
- 📚 Comprehensive documentation

## 🚀 Getting Started

### System Requirements
| Requirement | Version |
|------------|---------|
| WordPress  | 6.7.1+  |
| PHP        | 8.3.3+  |
| Node.js    | 20.14.0+|

### ⚡️ Quick Install

#### 🔷 WordPress Plugin Directory
1. 📁 Navigate to **Plugins** > **Add New** in WordPress
2. 🔍 Search for "WP SEO Analyzer"
3. ⚡️ Click "Install Now" then "Activate"

#### 🔷 Manual Installation
1. 📥 Download the [latest release](https://github.com/yourusername/wp-seo-analyzer/releases)
2. 📁 Go to **Plugins** > **Add New** > **Upload Plugin**
3. 📤 Upload the zip file
4. ✨ Click "Install Now" then "Activate"

## 🎮 Usage Guide

### 🖥️ WordPress Admin Interface

1. 📊 Access **SEO Analyzer** in your admin menu
2. 🔑 Enter your target keyword
3. 📑 Choose post type(s) to analyze
4. 🚀 Click "Analyze"
5. 📈 View your results:
   - 🔄 Sort by any column
   - ⚡️ Filter results instantly
   - 📱 Responsive on all devices
   - 📊 Export capabilities

### 🔌 REST API Integration

Base URL: `http://your-site.com/wp-json/wp-seo-analyzer/v1`

#### 🔑 Authentication
```http
GET /get-nonce
```
Returns a nonce token for authenticated requests.

#### 📊 Content Analysis
```http
GET /analyze
```
Parameters:
- `keyword` (required): Your target keyword
- `post_type` (optional): Content type to analyze
- `show_only_keyword` (optional): Filter by keyword presence

#### 📑 Post Types
```http
GET /post-types
```
Retrieves available content types.

### 🧪 API Testing

We provide a comprehensive Postman collection for testing:

1. 📥 Import the collection from `postman/`
2. 🔧 Configure the environment
3. 🚀 Start testing:
   1. Get your nonce token
   2. Set up authentication
   3. Explore endpoints

## 👩‍💻 Development

### 🛠️ Setup
```bash
# Clone repository
git clone https://github.com/yourusername/wp-seo-analyzer.git

# Install dependencies
npm install

# Start development
npm run start
```

### 📦 Build
```bash
# Production build
npm run build

# Create plugin package
npm run plugin-zip
```

### 📁 Project Structure
```
wp-seo-analyzer/
├── 📦 build/                # Compiled files
├── 📂 includes/            # PHP classes
├── 🌐 languages/          # Translations
├── 📱 src/                # Source files
│   ├── 🧩 blocks/        # Gutenberg blocks
│   ├── 🎨 components/    # React components
│   ├── 📄 index.js      # Main entry
│   └── 🎨 style.scss    # Styles
└── 📝 wp-seo-analyzer.php # Plugin main
```

## ✅ Features Checklist

### 📊 Content Analysis
- [x] 📝 Word count calculation
- [x] 🎯 Keyword density analysis
- [x] 📚 Multi post type support
- [x] ⚡️ Real-time processing

### 🎨 Frontend
- [x] 📊 Interactive tables
- [x] 🔍 Advanced filtering
- [x] 📱 Responsive design
- [x] 📄 Smart pagination

### 🔌 API
- [x] 🔒 Secure endpoints
- [x] 📚 WordPress standards
- [x] ⚡️ Performance optimized
- [x] 🛡️ Authentication

### 👨‍💻 Code Quality
- [x] 🏗️ OOP architecture
- [x] 📦 Modern namespaces
- [x] ✨ Coding standards
- [x] 🔧 React best practices

## 🤝 Contributing

Here's how you can help:

1. 🍴 Fork the repository
2. 🌿 Create your feature branch
3. ✨ Make your changes
4. 🚀 Push to your fork
5. 📬 Open a Pull Request

## 📄 License

Licensed under GPL-2.0 - see the [LICENSE](LICENSE) file for details.


---