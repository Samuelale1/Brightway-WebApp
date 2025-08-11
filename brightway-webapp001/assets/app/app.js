
let name = "samuel"
const age = 20
let isStudent= true
let undefinedValue
let nullValue=null

console.log(name ,typeof(name))
console.log(age ,typeof(age))
console.log(isStudent ,typeof(isStudent))
console.log(undefinedValue ,typeof(undefinedValue))
console.log(nullValue ,typeof(nullValue))

var score =prompt("What is your score ")
console.log(typeof score) 
score = Number(score)
console.log(typeof score) 

if(score){
    if(score >= 90){
        console.log(`You have an A because your score is ${score}`)
    }
    else if(score <90 && score>=50){
        console.log(`You have a B because your score is ${score}`)
    }
    else if(score <=49 && score>=40){
        console.log(`You have a c because your score is ${score}`)
    }
    else{
        console.log(`You fail because your score is ${score}`)
    }
}
