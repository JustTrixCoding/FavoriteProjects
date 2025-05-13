## 1. Adding to Shopping Cart

### Test Case TC-1: Add a single product to the cart  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Navigate to the product page |  |
| 2 | Click 'Add to Cart' on a product |  |
| 3 | Navigate to the cart | Product appears in the shopping cart |

---

### Test Case TC-2: Add multiple products to the cart  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Add multiple products to the cart |  |
| 2 | Navigate to the cart | All selected products should be displayed in the cart |

---

### Test Case TC-3: Add the same product multiple times  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Add the same product multiple times |  |
| 2 | Navigate to the cart | Quantity should increase instead of duplicating the product |

---

## 2. Removing from Shopping Cart

### Test Case TC-1: Remove a product from the cart  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Add a product to the cart |  |
| 2 | Click 'Remove' in the cart | Product is removed from cart and database |

---

### Test Case TC-2: Remove all products one by one  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Add multiple products to the cart |  |
| 2 | Remove each product individually | The cart should be empty after all are removed |

---

## 3. Empting Shopping Cart

### Test Case TC-1: Empty the shopping cart  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Add multiple products to the cart |  |
| 2 | Click 'Empty Cart' | The cart should be completely empty |

---

### Test Case TC-2: Attempt to empty an already empty cart  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Click 'Empty Cart' when the cart is already empty | No change should occur, and no error should appear |

---

## 4. Updating Quantity

### Test Case TC-1: Increase product quantity  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Add a product to the cart |  |
| 2 | Increase the quantity by pressing the '+' button | The updated quantity should reflect in the cart |

---

### Test Case TC-2: Decrease product quantity  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Add a product to the cart |  |
| 2 | Decrease the quantity by pressing the '-' button | The updated quantity should reflect in the cart |

---

### Test Case TC-3: Attempt to decrease the quantity past 1  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Add a product to the cart |  |
| 2 | Set the quantity to 1 |  |
| 3 | Attempt to decrease the quantity by pressing the '-' button | This should remove the product from the shopping cart |

---

## 5. Shopping Cart Page

### Test Case TC-1: Ensure the shopping cart page loads correctly  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Navigate to the shopping cart page | Page loads with all cart items displayed |

---

### Test Case TC-2: Displays the correct total price of all items  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Add multiple different products to the cart |  |
| 2 | Verify that the total price updates correctly based on quantity and price | The total price should be correctly calculated |

---

### Test Case TC-3: Displays the correct total price of one item  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Add multiple quantities of one product to the cart |  |
| 2 | Verify that the total price updates correctly based on quantity and price | The total price should be correctly calculated |

---

## 6. Placing an Order

### Test Case TC-1: Place an order with items in the cart  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Add items to cart |  |
| 2 | Proceed to checkout |  |
| 3 | Enter valid personal details |  |
| 4 | Submit order | Displays the order_confirmation.php page. Additionally, the order will appear on the barber side |

---

### Test Case TC-2: Attempt to place an order with an empty cart  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Navigate to checkout with no items in the cart |  |
| 2 | Attempt to submit order | The checkout button won't appear |

---

### Test Case TC-3: Attempt to place an order with missing first name  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Add items to cart |  |
| 2 | Leave first name field empty |  |
| 3 | Attempt to submit order | Error message appears: "Please fill out this field" |

---

### Test Case TC-4: Attempt to place an order with missing last name  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Add items to cart |  |
| 2 | Leave last name field empty |  |
| 3 | Attempt to submit order | Error message appears: "Please fill out this field" |

---

### Test Case TC-5: Attempt to place an order with an invalid first name  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Add items to cart |  |
| 2 | Enter an invalid first name (e.g., "A", "A@") |  |
| 3 | Attempt to submit order | Error message appears: "First Name must be between 2 and 50 characters" OR "First name can only contain letters, spaces, hyphens, or apostrophes." |

---

### Test Case TC-6: Attempt to place an order with an invalid last name  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Add items to cart |  |
| 2 | Enter an invalid last name (e.g., "A", "A@") |  |
| 3 | Attempt to submit order | Error message appears: "Last Name must be between 2 and 50 characters" OR "Last name can only contain letters, spaces, hyphens, or apostrophes." |

---

### Test Case TC-7: Attempt to place an order with no email  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Add items to cart |  |
| 2 | Leave email field blank |  |
| 3 | Attempt to submit order | Error message appears: "Email cannot be empty." |

---

### Test Case TC-8: Attempt to place an order with a non-valid email pattern  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Add items to cart |  |
| 2 | Enter invalid email (e.g., "test@") |  |
| 3 | Attempt to submit order | Error message appears: "Please enter a valid email address (e.g., user@example.com)." |

---

### Test Case TC-9: Attempt to place an order with missing phone number  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Add items to cart |  |
| 2 | Leave phone number field empty |  |
| 3 | Attempt to submit order | Error message appears: "Phone number cannot be empty." |

---

### Test Case TC-10: Attempt to place an order with an invalid phone number  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Add items to cart |  |
| 2 | Enter an invalid phone number (e.g., "abc123") |  |
| 3 | Attempt to submit order | Error message appears: "Please enter a valid phone number" |

---

### Test Case TC-11: Place an order with additional instructions  
**Status: Passed**  
| # | Steps | Expected Result |
|---|-------|-----------------|
| 1 | Add items to cart |  |
| 2 | Enter valid personal and shipping details |  |
| 3 | Add special instructions (e.g., "test") |  |
| 4 | Submit order | Displays the order_confirmation.php page. Additionally, the order will appear on the barber side |