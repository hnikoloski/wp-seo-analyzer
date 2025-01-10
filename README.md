# WordPress SEO Analyzer

A high-performance WordPress plugin for programmatic SEO analysis. Analyze your content's word count, keyword density, and other SEO metrics through an intuitive interface and REST API.

## 🚀 Features

- **Content Analysis**
  - Word count calculation
  - Keyword density analysis
  - Post type filtering
  - Real-time search functionality

- **API Integration**
  - RESTful endpoints
  - Bearer token authentication
  - JSON responses
  - Comprehensive error handling

- **User Interface**
  - Modern React-based dashboard
  - Sortable and filterable table
  - Responsive design
  - Pagination for large datasets

- **Developer Features**
  - REST API endpoints
  - OOP architecture
  - WordPress coding standards
  - Extensible design

## 🔧 Requirements

- WordPress 5.8+
- PHP 7.4+ - (built on v8.3.3)
- Node.js 14+ - (built on v20.14.0)
- npm 6+

## 📦 Installation

### Option 1: Install from ZIP

1. Download the latest release zip file
2. Go to WordPress admin → Plugins → Add New → Upload Plugin
3. Upload the zip file and activate the plugin

### Option 2: Development Setup

1. Clone the repository:
```bash
git clone [repository-url]
cd wp-content/plugins/wp-seo-analyzer
```

2. Install dependencies:
```bash
npm install
```

3. Build assets:
```bash
npm run build
```

4. Activate the plugin in WordPress admin

### Creating Distribution ZIP

To create a distributable zip file:

```bash
# Build assets and create plugin zip
npm run eject
```

This will:
1. Build the latest assets
2. Create a zip file in the project root directory
3. Include all necessary plugin files
4. Exclude development files (node_modules, etc.)

The generated zip file can be directly uploaded through the WordPress plugin installer.

## 🎯 Usage

### Dashboard

1. Go to WordPress admin
2. Find "SEO Analyzer" in the menu
3. Enter a keyword to analyze
4. Use filters and sorting as needed

### Frontend Interface

The plugin provides a modern, React-based interface in the WordPress admin:

```
+----------------+------------+----------------+------------------+
| Post Title     | Word Count | Keyword Count | Keyword Density |
+----------------+------------+----------------+------------------+
| Sample Post 1  | 500        | 5            | 1.0%            |
| Sample Post 2  | 1200       | 8            | 0.67%           |
| Sample Post 3  | 800        | 6            | 0.75%           |
+----------------+------------+----------------+------------------+
```

Features:
- Click column headers to sort
- Use filters above the table to:
  - Search by keyword
  - Filter by post type
  - Show only posts containing the keyword
- Pagination controls for large datasets
- Responsive design for all screen sizes

### API Documentation

#### Authentication

The API uses Bearer token authentication:

1. Get your API token:
   - Go to WordPress admin → SEO Analyzer → Settings
   - Copy your API token
   - Keep this token secure and never share it publicly

2. Use the token in requests:
   - Add the `Authorization` header
   - Format: `Bearer your-token-here`

#### Available Endpoints

1. **Analyze Content**
   ```
   GET /wp-json/wp-seo-analyzer/v1/analyze
   ```
   Parameters:
   - `keyword` (required): Search term
   - `post_type` (optional): Filter by post type (default: 'all')
   - `show_only_keyword` (optional): Only show posts with keyword (default: false)

2. **Get Post Types**
   ```
   GET /wp-json/wp-seo-analyzer/v1/post-types
   ```
   Lists all available post types for filtering

#### Example Requests

1. **cURL**
   ```bash
   curl -X GET \
     'http://your-site.com/wp-json/wp-seo-analyzer/v1/analyze?keyword=example' \
     -H 'Authorization: Bearer your-token-here'
   ```

2. **Postman**
   - Method: GET
   - URL: `http://your-site.com/wp-json/wp-seo-analyzer/v1/analyze`
   - Headers:
     ```
     Authorization: Bearer your-token-here
     Content-Type: application/json
     ```
   - Query Parameters:
     ```
     keyword: your-keyword
     post_type: post (optional)
     show_only_keyword: true/false (optional)
     ```

#### Response Format

```json
{
  "posts": [
    {
      "id": 1,
      "title": "Sample Post",
      "word_count": 500,
      "keyword_density": 2.4,
      "url": "https://your-site.com/sample-post"
    }
  ],
  "total": 10
}
```

#### Error Responses

```json
{
  "code": "rest_forbidden",
  "message": "Invalid Bearer token.",
  "status": 401
}
```

Common errors:
- 401: Missing or invalid token
- 400: Missing required parameters
- 403: Insufficient permissions

## 🛠️ Development

### Scripts

- Development: `npm run start`
- Production: `npm run build`
- Lint: `npm run lint`

### Project Structure

```
wp-seo-analyzer/
├── includes/
│   ├── Plugin.php     # Core functionality
│   ├── Api.php        # API endpoints
│   └── Settings.php   # Plugin settings
├── src/
│   ├── index.js      # React app
│   └── style.scss    # Styles
└── wp-seo-analyzer.php # Plugin bootstrap
```

### Extending

Add custom metrics in `Plugin.php`:
```php
public function analyze_content($post, $keyword) {
    return [
        'word_count' => $this->count_words($post->post_content),
        'keyword_density' => $this->calculate_density($post->post_content, $keyword),
        'your_metric' => $this->your_analysis($post)
    ];
}
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

## 📝 License

GPL v2 or later

## 🙏 Credits

Built with:
- [WordPress](https://wordpress.org)
- [React](https://reactjs.org)
- [React Table](https://react-table.tanstack.com)