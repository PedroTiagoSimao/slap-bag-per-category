# ACF Field Setup Guide

This guide will help you set up the required ACF field for the SLAP - Saco por restaurante plugin.

## Prerequisites

- Advanced Custom Fields (ACF) plugin must be installed and activated
- WooCommerce must be installed and activated

## Step-by-Step Setup

### 1. Create a New Field Group

1. Go to **Custom Fields > Field Groups** in your WordPress admin
2. Click **Add New**
3. Name the field group: "Product Category Bag Fee"

### 2. Add the Field

1. Click **Add Field**
2. Configure the field with these settings:
   - **Field Label**: `Valor do Saco` (or your preferred label)
   - **Field Name**: `valor_saco` (this is required - do not change)
   - **Field Type**: `Number`
   - **Required**: No
   - **Default Value**: (leave empty)
   - **Placeholder Text**: `0.00`
   - **Prepend**: (leave empty or add currency symbol)
   - **Append**: `€` (or your currency symbol)
   - **Min**: `0`
   - **Max**: (leave empty)
   - **Step Size**: `0.01`

### 3. Set Location Rules

1. Scroll down to **Location** section
2. Set the rule to:
   - **Show this field group if**: `Taxonomy Term` is equal to `Product Category`

### 4. Configure Field Group Settings

1. Scroll down to **Settings** section
2. Configure as needed:
   - **Style**: Seamless (recommended)
   - **Position**: Normal
   - **Label Placement**: Top
   - **Instruction Placement**: Label

### 5. Publish

1. Click **Publish** to save your field group

## Usage

Once the field is set up:

1. Go to **Products > Categories**
2. Edit any category
3. You'll see the "Valor do Saco" field
4. Enter the bag fee amount (e.g., `0.10` for 10 cents)
5. Update the category

Now, when products from this category are added to the cart, the bag fee will be automatically applied!

## Example Values

- **Takeaway Containers**: 0.10 €
- **Plastic Bags**: 0.15 €
- **Special Packaging**: 0.25 €

## Troubleshooting

- **Field not showing**: Make sure the location rule is set to "Product Category" taxonomy
- **Fee not appearing in cart**: Verify the field name is exactly `valor_saco`
- **Fee showing as 0**: Make sure you've entered a numeric value greater than 0
