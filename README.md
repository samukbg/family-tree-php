# Family Tree Project

This project is a dynamic and interactive family tree application that allows users to visualize and manage their family connections. It is built with PHP, JavaScript, and Tailwind CSS, and it features a clean, modern interface.

## 🌐 Live Demo

Try the application live at: **[https://family-tree.wasmer.app/](https://family-tree.wasmer.app/)**

## Features

- **Multiple Family Trees:** Create and manage multiple family trees from the main dashboard.
- **Interactive Tree Views:** Switch between vertical and horizontal layouts to explore each family tree.
- **Dynamic Content:** Add, edit, and delete family members through an intuitive interface.
- **Multilingual Support:** The application supports 5 languages - English (🇺🇸), Portuguese Brazilian (🇧🇷), Estonian (🇪🇪), French (🇫🇷), and German (🇩🇪) with URL persistence.
- **Image Uploads:** Personalize each family member's card with a photo and add cover images for each tree.
- **Language Persistence:** Selected language is maintained across navigation and stored in URL parameters.
- **Tree Management:** Create, edit, and delete family trees with custom titles and multilingual descriptions.

## Screenshots

Here are a few screenshots of the application in action:

### Main Dashboard (Family Trees)
![Family Trees Dashboard](./screenshot-1.png)

### Individual Tree View
![Individual Family Tree](./screenshot-2.png)

## Getting Started

To get started with this project, you will need a local web server with PHP support.

1. **Clone the repository:**
   ```bash
   git clone https://github.com/samukbg/family-tree-php.git
   ```

2. **Navigate to the project directory:**
   ```bash
   cd family-tree
   ```

3. **Start your local web server** and navigate to the project directory in your browser.

## Project Structure

### Main Files
- `index.php`: Main dashboard displaying all family trees.
- `tree.php`: Individual family tree viewer (accessed via `tree.php?id=tree_id&lang=language`).
- `style.css`: Contains all the styles for the application.
- `trees.json`: Stores metadata for all family trees.

### Data Files
- `data.json`: Default family tree data (fallback).
- `site_data.json`: Stores editable titles and subtitles for trees.
- `images/photos.json`: Maps person names to their photo filenames.
- `tree_images/`: Directory containing cover images for family trees.

### Backend Scripts
- `manage_trees.php`: Handles creation, editing, and deletion of family trees.
- `add_person.php`, `edit_person.php`, `delete_person.php`: Scripts for managing family members.
- `upload_image.php`, `delete_photo.php`, `get_photos.php`: Scripts for handling image uploads.
- `update_text.php`: Script for saving editable text content.

## Example Files

The project includes example JSON files to help you get started:

- `data.example.json` - Sample family tree data
- `site_data.example.json` - Sample site configuration
- `images/photos.example.json` - Sample photo mappings
- `trees_example.json` - Sample tree metadata

Copy these files to their corresponding non-example filenames (e.g., `trees.json`) to start with a sample data set.

## URL Structure

- **Main Dashboard:** `index.php?lang=en` (or other language codes)
- **Individual Tree:** `tree.php?id=tree_id&lang=en`
- **Language Parameter:** Supported languages are `en`, `pt`, `et`, `fr`, `de`

## Navigation Flow

1. **Main Dashboard** - View all available family trees
2. **Tree Selection** - Click on a tree to view its members  
3. **Tree Management** - Add, edit, or delete family members
4. **Language Switching** - Language selection persists across all pages
5. **Back Navigation** - Return to main dashboard while preserving language

## How to Install and Run

1. **Prerequisites:**
   - A web server with PHP support (e.g., Apache, Nginx).
   - PHP 7.4 or higher.

2. **Installation:**
   - Clone this repository to your local machine or web server.
   - Ensure that the web server has write permissions to the `data.json`, `site_data.json`, and `images/photos.json` files, as well as the `images` directory.

3. **Running the Application:**
   - Navigate to the project directory in your web browser to access the main dashboard.
   - Create your first family tree using the "Add New Tree" option.
   - Click on any tree to view and manage individual family members.
   - To start with sample data, copy the contents of the `.example.json` files to their corresponding non-example filenames.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
