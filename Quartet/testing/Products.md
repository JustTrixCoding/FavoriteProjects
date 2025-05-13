## 1. Adding Product

### Test Case TC-1: Add a valid product  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Enter a product name and description |  |
| 2 | Set a valid price |  |
| 3 | Upload a valid image |  |
| 4 | Click ‘Add Product’ | - Product successfully added and appears in the product list<br>- Should reflect in the database |

---

### Test Case TC-2: Add a product with missing name  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Leave product name blank |  |
| 2 | Enter the rest of the information |  |
| 3 | Click ‘Add Product’ | Error message appears: “Please fill out this field” |

---

### Test Case TC-3: Add a product with a long name  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Enter a long product name |  |
| 2 | Enter valid description, price and image |  |
| 3 | Click ‘Add Product’ | Error message appears: “Maximum 70 characters allowed” |

---

### Test Case TC-4: Add a product with a missing description  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Enter a valid product name, price, and image |  |
| 2 | Don’t enter a description |  |
| 3 | Click ‘Add Product’ | Error message appears: “Please fill out this field” |

---

### Test Case TC-5: Add a product with a negative price  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Enter a valid product name, description, and image |  |
| 2 | Enter a negative price |  |
| 3 | Click ‘Add Product’ | Error message appears: “Price must be a positive number” |

---

### Test Case TC-6: Add a product with a zero price  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Enter a valid product name, description, and image |  |
| 2 | Enter a price of zero |  |
| 3 | Click ‘Add Product’ | Error message appears: “Price must be a positive number” |

---

### Test Case TC-7: Add a product with a non-digit price  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Enter a valid product name, description, and image |  |
| 2 | Enter a price with no digits |  |
| 3 | Click ‘Add Product’ | No error message. Simply won’t allow that input |

---

### Test Case TC-8: Add a product with an invalid file type  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Enter a valid product name, description, and price |  |
| 2 | Enter a pdf file |  |
| 3 | Click ‘Add Product’ | Error message appears: “Only JPEG, PNG, and GIF images are allowed.” |

---

### Test Case TC-9: Add a product with a large file  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Enter a valid product name, description, and price |  |
| 2 | Enter a file that is larger than 10 MB |  |
| 3 | Click ‘Add Product’ | Error message appears: “File size must be less than 10MB.” |

---

### Test Case TC-10: Add a product with no image  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Enter a valid product name, description, and price |  |
| 2 | Don’t upload an image |  |
| 3 | Click ‘Add Product’ | Error message appears: “Please upload an image.” |


## 2. Editing Products

---

### Test Case TC-1: Edit product details successfully  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Select an existing product |  |
| 2 | Modify |  |
| 3 | Upload a valid image |  |
| 4 | Click ‘Update Product’ | - Product successfully added and appears in the product list<br>- Should reflect in the database |

---

### Test Case TC-2: Remove the product name  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Select an existing product |  |
| 2 | Delete the product name |  |
| 3 | Click ‘Update Product’ | Error message appears: “Please fill out this field” |

---

### Test Case TC-3: Rename the product name with a long name  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Select an existing product |  |
| 2 | Enter a name longer than 70 characters |  |
| 3 | Click ‘Update Product’ | Error message appears: “Maximum 70 characters allowed” |

---

### Test Case TC-4: Remove the product description  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Select an existing product |  |
| 2 | Delete the product description |  |
| 3 | Click ‘Update Product’ | Error message appears: “Please fill out this field” |

---

### Test Case TC-5: Remove the product price  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Select an existing product |  |
| 2 | Delete the product price |  |
| 3 | Click ‘Update Product’ | Error message appears: “Price must be a positive number” |

---

### Test Case TC-6: Edit the price to be negative  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Select an existing product |  |
| 2 | Change the price to zero |  |
| 3 | Click ‘Update Product’ | Error message appears: “Price must be a positive number” |

---

### Test Case TC-7: Edit the price to be zero  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Select an existing product |  |
| 2 | Change the price to zero |  |
| 3 | Click ‘Update Product’ | Error message appears: “Price must be a positive number” |

---

### Test Case TC-8: Edit the price to be a non-digit  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Select an existing product |  |
| 2 | Enter a non-numeric value in the price field |  |
| 3 | Click ‘Update Product’ | No error message. Simply won’t allow that input |

---

### Test Case TC-9: Update the product without adding a new image  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Select an existing product |  |
| 2 | Modify other details but leave the image unchanged |  |
| 3 | Click ‘Update Product’ | Will keep the original image |

---

### Test Case TC-10: Add a product with an invalid file type  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Select an existing product |  |
| 2 | Upload an invalid file format (e.g., pdf) |  |
| 3 | Click ‘Update Product’ | Error message appears: “Only JPEG, PNG, and GIF images are allowed.” |

---

### Test Case TC-11: Add a product with a large file  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Select an existing product |  |
| 2 | Upload a file larger than 10MB |  |
| 3 | Click ‘Update Product’ | Error message appears: “File size must be less than 10MB.” |

## 3. Remove Product

---

### Test Case TC-1: Delete an existing product  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Select an existing product |  |
| 2 | Click 'Delete' |  |
| 3 | Confirm deletion | Product is removed from the database and product list |

---

### Test Case TC-2: Once a product is removed it should reflect in the shopping cart  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Delete a product that was added to the cart |  |
| 2 | Navigate to the shopping cart | The product removed should also be removed from the shopping cart |

---

## 4. Product Page

---

### Test Case TC-1: Displays all products  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Navigate to the product page | Displays all the products in the database |

---

### Test Case TC-2: Displays a confirmation page when deleting a product  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Click 'Delete' on a product |  |
| 2 | Observe the response | A confirmation pop-up should appear before deleting the product |
