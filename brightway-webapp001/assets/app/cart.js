/* 
* Holds all cart behaviors
*
 */

function addToCart(productId) {
    fetch('../Orders/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ productId: productId }),
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message); // You can use a toast or popup here
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

