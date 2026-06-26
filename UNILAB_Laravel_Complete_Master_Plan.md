# UNI-LAB MARKET
# Complete Laravel Architecture & Development Blueprint

## Version 1.0

---

# Project Goal

Build a high-performance, scalable, SEO-friendly medical e-commerce platform using Laravel 11/12.

Core principles:

- Dynamic categories
- Unlimited category levels
- Dynamic category pages
- Dynamic SEO
- Dynamic page builder
- Multi-image products
- Optimized database queries
- Low RAM usage
- Low CPU usage
- Fast search
- Fast loading
- Enterprise-ready architecture

---

# SYSTEM ARCHITECTURE

Categories are fully dynamic.

Examples:

Medicine
├── Stethoscopes
├── Blood Pressure Devices
└── Glucose Meters

Pharmacy
├── Laboratory Glassware
├── Balances
└── Lab Equipment

No category is hardcoded.

Every category automatically creates its own page.

---

# DATABASE STRUCTURE

## categories

Fields:

- id
- parent_id
- name
- slug
- image
- banner
- description
- primary_color
- secondary_color
- seo_title
- seo_description
- seo_keywords
- canonical_url
- og_title
- og_description
- og_image
- schema_markup
- status
- created_at
- updated_at

Indexes:

- slug
- parent_id
- status

---

## products

Fields:

- id
- category_id
- name
- slug
- sku
- short_description
- description
- price
- sale_price
- stock
- featured
- seo_title
- seo_description
- seo_keywords
- canonical_url
- og_title
- og_description
- og_image
- schema_markup
- status
- created_at
- updated_at

Indexes:

- slug
- category_id
- featured
- status
- price
- created_at

---

## product_images

Fields:

- id
- product_id
- image
- sort_order
- created_at
- updated_at

Indexes:

- product_id

---

## page_sections

Fields:

- id
- category_id
- section_type
- title
- content
- image
- sort_order
- status
- created_at
- updated_at

---

# MIGRATIONS

Required migrations:

1. categories
2. products
3. product_images
4. page_sections

Use:

php artisan make:migration

for each table.

---

# ELOQUENT MODELS

Category

Relations:

- parent()
- children()
- products()
- sections()

Product

Relations:

- category()
- images()

ProductImage

Relations:

- product()

PageSection

Relations:

- category()

---

# CONTROLLERS

Admin Controllers

- DashboardController
- CategoryController
- ProductController
- PageSectionController

Frontend Controllers

- CategoryController
- ProductController

---

# ROUTES

Frontend

/category/{slug}

/product/{slug}

Admin

/admin/categories

/admin/products

/admin/sections

---

# CATEGORY MANAGEMENT

Admin can:

- Create category
- Edit category
- Delete category
- Reorder categories
- Upload banner
- Configure SEO
- Configure colors

---

# PRODUCT MANAGEMENT

Product Form

Basic Data:

- Name
- Slug
- SKU
- Category
- Price
- Sale Price
- Stock

Content:

- Short Description
- Description

Media:

- Multiple Images

SEO:

- SEO Title
- Description
- Keywords
- Canonical URL
- OG Title
- OG Description
- OG Image
- Schema Markup

---

# MULTI IMAGE SYSTEM

Storage:

storage/app/public/products

Command:

php artisan storage:link

Generate:

- thumb
- medium
- large

Recommended sizes:

150x150

500x500

1200x1200

---

# DYNAMIC PAGE BUILDER

Each category page can contain:

- Banner
- Slider
- Featured Products
- Latest Products
- Gallery
- FAQ
- Video
- HTML Block
- Text Block

Admin controls:

- Visibility
- Sorting
- Content

No Blade editing required.

---

# SEO SYSTEM

Category SEO

Fields:

- SEO Title
- Meta Description
- Keywords
- Canonical URL
- OG Title
- OG Description
- OG Image
- Schema JSON-LD

---

Product SEO

Fields:

- SEO Title
- Meta Description
- Keywords
- Canonical URL
- OG Title
- OG Description
- OG Image
- Schema JSON-LD

---

# SEO IN BLADE

Layout variables:

$seo_title

$seo_description

$seo_keywords

$canonical_url

$og_title

$og_description

$og_image

$schema_markup

Every page can override SEO.

---

# ADVANCED SEO FEATURES

Admin-controlled:

- Sitemap XML
- Robots.txt
- Canonical URLs
- Open Graph
- Twitter Cards
- Breadcrumbs
- Redirect Manager
- Schema Markup

Automatic fallback generation.

---

# CHILD CATEGORY API

GET

/admin/categories/{id}/children

Returns:

JSON list of child categories.

Used in product forms.

---

# PERFORMANCE ARCHITECTURE

Goal:

100,000+ products

10,000+ categories

1,000,000+ images

with fast performance.

---

# QUERY RULES

Never use:

Product::all()

Category::all()

for frontend pages.

---

Always use:

paginate()

simplePaginate()

cursorPaginate()

---

Use select()

instead of loading all columns.

Example:

Only retrieve:

- id
- name
- slug
- price

when displaying listings.

---

# N+1 PREVENTION

Always use:

with()

Example:

category

images

instead of lazy loading.

---

# EAGER LOADING RULES

Load only needed columns.

Never load unused relations.

---

# DATABASE INDEXING

Must index:

Categories

- slug
- parent_id
- status

Products

- slug
- category_id
- status
- featured
- price
- created_at

Images

- product_id

---

# CACHING STRATEGY

Cache:

Main Categories

24 Hours

Site Settings

Forever

Category Pages

30 Minutes

Popular Products

1 Hour

---

# SEARCH OPTIMIZATION

Small stores:

LIKE queries

Large stores:

Laravel Scout

Meilisearch

Optional:

Elasticsearch

---

# IMAGE OPTIMIZATION

Never show original image directly.

Use:

thumb

medium

large

Enable:

lazy loading

WebP conversion

Image compression

---

# MEMORY OPTIMIZATION

Never use:

all()

for large datasets.

Use:

paginate()

chunk()

cursor()

lazy()

---

# CPU OPTIMIZATION

Heavy tasks must run in Queue.

Examples:

- Image processing
- Sitemap generation
- Notifications
- SEO generation

---

# QUEUE SYSTEM

Laravel Queue

Recommended drivers:

- Database
- Redis

---

# API OPTIMIZATION

Use API Resources.

Return only needed fields.

Avoid unnecessary timestamps.

---

# MONITORING TOOLS

Development only:

- Laravel Telescope
- Laravel Debugbar

Production:

- Horizon
- Sentry

---

# ADMIN PANEL STRUCTURE

Dashboard

Categories

Products

Page Builder

SEO

Settings

Reports

Media Library

---

# FILE STRUCTURE

app/
├── Models
├── Services
├── Repositories
├── Http
│   └── Controllers

resources/
├── views
│   ├── admin
│   ├── category
│   ├── product
│   └── layouts

storage/
└── app/public/products

---

# DEVELOPMENT ROADMAP

Phase 1

Database Design

Phase 2

Migrations

Phase 3

Models

Phase 4

Relationships

Phase 5

Category CRUD

Phase 6

Product CRUD

Phase 7

Multi Images

Phase 8

Category Pages

Phase 9

Product Pages

Phase 10

SEO System

Phase 11

Page Builder

Phase 12

Search System

Phase 13

Caching

Phase 14

Queue System

Phase 15

Performance Testing

Phase 16

Production Deployment

---

# FINAL TARGET

Enterprise-level architecture.

Fast.

Scalable.

SEO-friendly.

Low resource consumption.

Laravel 12 ready.

Supports future expansion without rebuilding the system.












    